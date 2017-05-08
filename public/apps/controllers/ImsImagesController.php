<?php

use ImsGames as Games;
use ImsImages as Images;
use ImsTags as Tags;
use ImsLabels as Labels;
use Phalcon\Image\Adapter\Gd as Image;
use Phalcon\Mvc\Url;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class ImsImagesController extends ImsBaseController
{

    protected $url;

    public function initialize()
    {
        parent::initialize();
        $this->url = new Url;
        $this->url->setBaseUri(get_current_url());
    }

    public function indexAction()
    {

        $type = $this->request->get('type', 'string');
        $game_id = $this->request->get('game_id', 'int');
        $year = $this->request->get('year', 'int');
        $month = $this->request->get('month', 'int');
        $date = $this->request->get('date', 'int');

        if ($game_id) {
            // 赛事ID
            $conditions[] = 'id = :game_id:';
            $bind['game_id'] = $game_id;
        } elseif ($type) {
            // 类型
            $conditions[] = 'type = :type:';
            $bind['type'] = $type;
            if ($date) {
                // 类型+（年+月+日）
                $conditions[] = 'date = :date:';
                $bind['date'] = $date;
            } else {
                // 类型+（年）
                if ($year) {
                    $conditions[] = 'YEAR(date) = :year:';
                    $bind['year'] = $year;
                    if ($month) {
                        // 类型+（年+月）
                        $conditions[] = 'MONTH(date) = :month:';
                    }
                }
            }
        }

        if (empty($conditions) || empty($bind)) {
            return $this->response->setJsonContent([]);
        } else {
            $games = Games::find([
                'conditions' => implode(' AND ', $conditions),
                'bind'       => $bind,
                'columns'    => 'id',
            ])->toArray();
            $game_ids = (array)array_map(function ($game) {
                return $game['id'];
            }, $games);

            $images = Images::find([
                'conditions' => 'game_id IN({game_ids:array})',
                'bind' => [
                    'game_ids' => array_values($game_ids),
                ],
                'order' => 'updated_at DESC',
            ]);
        }

        $result = [];
        foreach ($images as $image) {
            $result[] = [
                'id'      => $image->id,
                'name'    => $image->name,
                'thumb'   => $this->url->get($image->thumb),
                'url'     => $this->url->get($image->url),
                'type'    => $image->game->type,
                'game_id' => $image->game_id,
                'tags'    => $image->tags->toArray(),
            ];
        }
        return $this->response->setJsonContent($result);
    }

    public function searchAction()
    {
        //$name = $this->request->get('name');
        $start_time = $this->request->get('start_time');

        $page = $this->request->get('page', 'int');
        $tags_names = (array)$this->request->get('tags');
        // 所有指定时间的比赛
        if ($start_time) {
            $games = Games::find([
                'conditions' => 'date > :start_time:',
                'bind' => [
                    'start_time' => $start_time
                ],
            ]);
        } else {
            $games = Games::find();
        }
        foreach ($games as $game) {
            $GameIds[] = $game->id;
        }
        if (empty($GameIds)) {
            return $this->response->setJsonContent([]);
        }
        $image_ids = array_map(function ($tag_name) use ($GameIds) {
            // 赛程
            $game_ids = [];
            $label = Labels::findFirstByName($tag_name);
            if ($label) {
                $game_ids = array_merge($game_ids, array_map(function ($game) use ($GameIds) {
                    return in_array($game['id'], $GameIds) ? $game['id'] : null;
                }, $label->games->toArray()));
            }
            // 比赛名
            $games = Games::find([
                'conditions' => 'name LIKE :name:',
                'bind' => [
                    'name' => '%' . $tag_name . '%',
                ],
            ]);
            if ($games) {
                $game_ids = array_merge($game_ids, array_map(function ($game) use ($GameIds) {
                    return in_array($game['id'], $GameIds) ? $game['id'] : null;
                }, $games->toArray()));
            }
            $game_ids = array_values(array_unique($game_ids));
            $image_ids = [];
            // 按照比赛名和赛程名搜索图片
            if ($game_ids) {
                $games = Games::find([
                    'conditions' => 'id IN({ids:array})',
                    'bind' => [
                        'ids' => $game_ids,
                    ],
                ]);
                foreach ($games as $game) {
                    $image_ids = array_merge($image_ids, array_map(function ($image) use ($GameIds) {
                        return in_array($image['game_id'], $GameIds) ? $image['id'] : null;
                    }, $game->images->toArray()));
                }
            }
            // 标签
            $tag = Tags::findFirstByName($tag_name);
            if ($tag) {
                $image_ids = array_merge($image_ids, array_map(function ($image) use ($GameIds) {
                    return in_array($image['game_id'], $GameIds) ? $image['id'] : null;
                }, $tag->images->toArray()));
            }
            // 图片名
            $images = Images::find([
                'conditions' => 'name LIKE :name:',
                'bind' => [
                    'name' => '%' . $tag_name . '%',
                ],
            ]);
            if ($images) {
                $image_ids = array_merge($image_ids, array_map(function ($image) use ($GameIds) {
                    return in_array($image['game_id'], $GameIds) ? $image['id'] : null;
                }, $images->toArray()));
            }
            $image_ids = array_values(array_unique($image_ids));
            return $image_ids;
        }, $tags_names);
        if (count($image_ids) > 1) {
            $image_ids = array_values(call_user_func_array('array_intersect', $image_ids));
        } else {
            $image_ids = $image_ids[0];
        }
        if (empty($image_ids)) {
            return $this->response->setJsonContent([]);
        }

        $images = Images::find([
            'conditions' => 'id IN({ids:array})',
            'bind' => [
                'ids' => $image_ids,
            ],
            'order' => 'updated_at DESC',
        ]);
        $paginator = new PaginatorModel(
            [
                "data"  => $images,
                "limit" => 20,
                "page"  => $page,
            ]
        );
        $images = $paginator->getPaginate();
        if ($images->last < $page) {
            return $this->response->setJsonContent([]);
        }
        $result = [];
        foreach ($images->items as $image) {
            $result[] = [
                'id'      => $image->id,
                'name'    => $image->name,
                'thumb'   => $this->url->get($image->thumb),
                'url'     => $this->url->get($image->url),
                'type'    => $image->game->type,
                'game_id' => $image->game_id,
                'tags'    => $image->tags->toArray(),
            ];
        }
        return $this->response->setJsonContent($result);
    }

    public function uploadAction()
    {
        // Check if the user has uploaded files
        if (!$this->request->hasFiles()) {
            $this->response->setJsonContent(['status' => 'error', 'data' => '没有需要上传的文件']);
        }
        $game_id = $this->request->getPost('game_id', 'int');
        $files = $this->request->getUploadedFiles();
        $result = [];
        foreach ($files as $file) {
            // echo $file->getName(), " ", $file->getSize(), "\n";
            // 白名单过滤图片拓展名
            $white_list = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp'];
            $file_type = strtolower($file->getType());
            if (!in_array($file_type, $white_list)) {
                continue;
            }
            // Move the file into the application
            $datepath = 'day_' . (new \DateTime('now', new \DateTimeZone('PRC')))->format('ymd'). '/';
            $storage = $this->config->server->storage . $datepath;
            $cdn = $this->config->server->cdn . $datepath;
            $file_name = $file->getName();
            $file_uuid = uniqid();
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = $storage . $file_uuid . '.' . $file_ext;
            $thumb_name = $storage . $file_uuid . '_thumb.' . $file_ext;
            mkdirs($storage);
            if (!$file->moveTo('./' . $new_file_name)) {
                return $this->response->setJsonContent(['status' => 'error', 'data' => '图片保存失败']);
            }
            // 生成缩略图
            $image = new Image('./' . $new_file_name);
            $image->resize(300, null, \Phalcon\Image::WIDTH);
            // 打水印
            // $image->insert('public/watermark.png');
            // 保存缩略图
            $image->save('./' . $thumb_name);
            try {
                $image = $this->store($file_name, $game_id, $cdn . $file_uuid . '.' . $file_ext, $cdn . $file_uuid . '_thumb.' . $file_ext);
                $result[$file_name] = $image->toArray();
                $result[$file_name]['tags'] = $image->getTags()->toArray();
                $result[$file_name]['game'] = $image->getGame()->toArray();
            } catch (\Exception $e) {
                return $this->response->setJsonContent(['status' => 'error', 'data' => $e->getMessage()]);
            }
        }
        if (empty($result)) {
            return $this->response->setJsonContent(['status' => 'error', 'data'=>'no image']);
        }
        return $this->response->setJsonContent(['status' => 'success', 'data' => $result]);
    }

    /**
     * API接口
     * 创建图片
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function createAction()
    {
        $secret    = $this->request->get('secret');
        $timestamp = $this->request->get('timestamp');
        if (!api_verify($secret, $timestamp, $this->config->server->key)) {
            return $this->response->setJsonContent(['status' => 'error', 'data' => '']);
        }

        $remote_game_id = $this->request->get('game_id');
        $name           = $this->request->get('name') ?: '未命名 ';
        $url            = $this->request->get('url');
        $thumb          = $this->request->get('thumb') ?: $url;

        $game = Games::findFirst([
            'conditions' => 'remote_game_id = ?1',
            'bind' => [
                1 => $remote_game_id,
            ],
        ]);
        if (!$game) {
            return $this->response->setJsonContent(['status' => 'error', 'data' => '比赛不存在，请先建立该场比赛']);
        }
        $this->store($name, $game->id, $url, $thumb);
        return $this->response->setJsonContent(['status' => 'success']);
    }

    public function storeAction()
    {

    }

    private function store($name, $game_id, $url, $thumb, $tags = []) {
        $game = Games::findFirst([
            'conditions' => 'id = ?1',
            'bind' => [
                1 => $game_id,
            ],
        ]);
        if (!$game) {
            throw new \Exception('指定比赛不存在');
        }
        $image = Images::findFirst([
            'conditions' => 'game_id = ?1 AND url = ?2',
            'bind' => [
                1 => $game_id,
                2 => $url,
            ],
        ]) ?: new Images;
        $image->name  = $name;
        $image->game  = $game;
        $image->url   = $url;
        $image->thumb = $thumb;
        // 自动添加标签
        $tags  = array_map(function ($tag) {
            return Tags::findFirst([
                'conditions' => 'name = ?1',
                'bind' => [
                    1 => $tag,
                ],
            ]);
        }, $tags);
        if (!$tags) {
            $tags = [];
            foreach ($image->game->tags as $tag) {
                $tags[] = $tag;
            }
        }
        $image->imagesTags->delete();
        $image->tags  = $tags;
        $image->updated_at = $image->created_at = (new \DateTime('now', new \DateTimeZone('PRC')))->format('Y-m-d H:i:s');
        $image->save();
        return $image;
    }

    public function showAction()
    {
        
    }

    public function editAction()
    {
        
    }

    /**
     * http://your_path/images/create?url=http://images_path&game_id=1&name=image_name&tag_id[]=1&tag_id[]=2
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function updateAction()
    {
        $id = $this->request->getPost('id', 'int');
        $name = $this->request->getPost('name', 'string');
        //$url = filter_var($this->request->getPost('url'),FILTER_VALIDATE_URL);
        $game_id = $this->request->getPost('game_id', 'int');
        $tags = (array)$this->request->getPost('tags');
        // 验证
        //$url || $error[] = 'URL不合法';
        $game = Games::findFirst(["id='${game_id}'"]);
        $game || $error[] = '指定赛事不存在'.$game_id;
        // empty($tag_names) || $error[] = '指定赛事不存在';
        if (!empty($error)) {
            $this->response->setJsonContent(['status' => 'error', 'data' => $error]);
            return $this->response;
        }
        // 存数据
        $image = Images::findFirst($id);
        $image->game = $game;
        $image->name = $name;
        //$image->url = $url;
//开启事务
        try {
            $this->db->begin();

            if (!$image->imagesTags->delete()) {
                $this->db->rollback();
                return $this->response->setJsonContent(['status' => 'error', 'data' => ['保存失败']]);
            }
            $image->tags = array_map(function ($tag_name) use ($image) {
                $tag = Tags::findFirst([
                    'conditions' => 'name=?1',
                    'bind' => [
                        1 => $tag_name,
                    ],
                ]);
                if (!$tag) {
                    $tag = new Tags;
                    $tag->name = $tag_name;
                    $tag->type = $image->game->type;
                    if (!$tag->save()) {
                        $this->db->rollback();
                        return $this->response->setJsonContent(['status' => 'error', 'data' => ['保存失败']]);
                    }
                }
                return $tag;
            }, $tags) ?: null;
            $image->updated_at = (new \DateTime('now', new \DateTimeZone('PRC')))->format('Y-m-d H:i:s');
            $result = $image->save();
            if (!$result) {
                $this->db->rollback();
                return $this->response->setJsonContent(['status' => 'error', 'data' => ['保存失败']]);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            return $this->response->setJsonContent(['status' => 'error', 'data' => $e->getMessage()]);
        }

        return $this->response->setJsonContent(['status' => 'success']);
    }

    /**
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function destroyAction()
    {
        $ids = (array)$this->request->get('ids', 'int');
        $images = Images::find([
            'conditions' => 'id IN({ids:array})',
            'bind' => [
                'ids' => $ids,
            ],
        ]);
        try {
            $this->db->begin();

            // 删除标签关联关系
            foreach ($images as $image) {
                if (!$image->imagesTags->delete()) {
                    $this->db->rollback();
                }
                unlink($image->thumb);
                unlink($image->url);
            }
            // 删除图片记录
            if (!$images->delete()) {
                $this->db->rollback();
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            return $this->response->setJsonContent(['status' => 'error', 'data' => $e->getMessage()]);
        }
        return $this->response->setJsonContent(['status' => 'success']);
    }
}
