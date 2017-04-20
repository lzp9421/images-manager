<?php

namespace Multiple\Frontend\Controllers;

use Multiple\Frontend\Models\ImsGames as Games;
use Multiple\Frontend\Models\ImsImages as Images;
use Multiple\Frontend\Models\ImsTags as Tags;
use Intervention\Image\ImageManagerStatic as Image;

class ImagesController extends BaseController
{

    public function indexAction()
    {
        $game_id = $this->request->get('game_id', 'int');
        $images = Images::find([
            'conditions' => 'game_id = ?1',
            'bind' => [
                1 => $game_id,
            ],
            'order' => 'updated_at DESC',
        ]);
        $result = [];
        foreach ($images as $image) {
            $result[] = [
                'id' => $image->id,
                'name' => $image->name,
                'thumb' => $image->thumb,
                'url' => $image->url,
                'type' => $image->game->type,
                'game_id' => $image->game_id,
                'tags' => $image->tags,
            ];
        }
        $this->response->setJsonContent($result);
        return $this->response;
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
            $storage = './storage/images/' . (new \DateTime('now', new \DateTimeZone('PRC')))->format('Y-m-d') . '/';
            $file_name = $file->getName();
            $file_uuid = uniqid();
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = $storage . $file_uuid . '.' . $file_ext;
            $thumb_name = $storage . $file_uuid . '_thumb.' . $file_ext;
            mkdirs($storage);
            if (!$file->moveTo($new_file_name)) {
                return $this->response->setJsonContent(['status' => 'error', 'data' => '图片保存失败']);
            }
            // 生成缩略图
            $image = Image::make($new_file_name);
            $image->fit(300);
            // 打水印
            // $image->insert('public/watermark.png');
            // 保存缩略图
            $image->save($thumb_name);
            try {
                $image = $this->store($file_name, $game_id, $new_file_name, $thumb_name);
                $result[$file_name] = $image->toArray();
                $result[$file_name]['tags'] = $image->getTags()->toArray();
                $result[$file_name]['game'] = $image->getGame()->toArray();
            } catch (\Exception $e) {
                return $this->response->setJsonContent(['status' => 'error', 'data' => $e->getMessage()]);
            }
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
        $token = $this->request->get('token');
        $key = '';
        if (!password_verify($key, $token)) {
            return $this->response->setJsonContent(['status' => 'error', 'data' => 'unauthorized']);
        }
        $name = $this->request->get('name') ?: '未命名 ';
        $game_name = $this->request->get('game');
        $url = $this->request->get('url');
        $tags = $this->request->get('tags');
        $game = Games::findFirst([
            'conditions' => 'name = ?1',
            'bind' => [
                1 => $game_name,
            ],
        ]);
        if (!$game) {
            return $this->response->setJsonContent(['status' => 'error', 'data' => '比赛不存在，请先建立该场比赛']);
        }
        $thumb = $url;
        $this->store($name, $game->id, $url, $thumb, $tags);
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
        $image = new Images;
        $image->name = $name;
        $image->game = $game;
        $image->url = $url;
        $image->thumb = $thumb;
        $image->tags = array_map(function ($tag) {
            return Tags::findFirst([
                'conditions' => 'id=?1',
                'bind' => [
                    1 => $tag,
                ],
            ]);
        }, $tags) ?: null;
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

        $image->imagesTags->delete();
        $image->tags = array_map(function ($tag) {
            return Tags::findFirst([
                'conditions' => 'name=?1',
                'bind' => [
                    1 => $tag,
                ],
            ]);
        }, $tags) ?: null;
        $image->updated_at = (new \DateTime('now', new \DateTimeZone('PRC')))->format('Y-m-d H:i:s');
        $result = $image->save();
        if (!$result) {
            return $this->response->setJsonContent(['status' => 'error', 'data' => ['保存失败']]);
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
