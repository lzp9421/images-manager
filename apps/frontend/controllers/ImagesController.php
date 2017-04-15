<?php

namespace Multiple\Frontend\Controllers;

use Multiple\Frontend\Models\Games;
use Multiple\Frontend\Models\Images;
use Multiple\Frontend\Models\Tags;

class ImagesController extends BaseController
{
    /*
     * GET	/photo	index	photo.index
GET	/photo/create	create	photo.create
POST	/photo	store	photo.store
GET	/photo/{photo}	show	photo.show
GET	/photo/{photo}/edit	edit	photo.edit
PUT/PATCH	/photo/{photo}	update	photo.update
DELETE	/photo/{photo}	destroy	photo.destroy
     */

    public function indexAction()
    {
        $game_id = $this->request->get('game_id', 'int');
        $images = Images::find([
            'conditions' => 'game_id = ?1',
            'bind' => [
                1 => $game_id,
            ],
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
        $files = $this->request->getUploadedFiles();
        $result = [];
        foreach ($files as $file) {
            // echo $file->getName(), " ", $file->getSize(), "\n";
            // 白名单过滤图片拓展名
            $name = $file->getName();
            $white_list = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp'];
            $file_type = strtolower($file->getType());
            if (!in_array($file_type, $white_list)) {
                continue;
            }
            // Move the file into the application
            $storage = './storage/images/' . (new \DateTime('now', new \DateTimeZone('PRC')))->format('Y-m-d') . '/';
            $this->mkdirs($storage);
            $result[$name] = $file->moveTo($storage . uniqid() . '.' . pathinfo($name, PATHINFO_EXTENSION));
        }
        return $this->response->setJsonContent(['status' => 'success', 'data' => $result]);
    }

    private function mkdirs($dir, $mode = 0777)
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
        if (!$this->mkdirs(dirname($dir), $mode)) return FALSE;
        return @mkdir($dir, $mode);
    }

    public function createAction()
    {

    }

    public function storeAction()
    {

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
        $tag_names = (array)$this->request->getPost('tag_names');
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
        foreach ($tag_names as $tag_name) {
            $tag = Tags::findFirst([
                'conditions' => 'name=?1',
                'bind' => [
                    1 => $tag_name,
                ],
            ]);
            $tag && $tags[] = $tag;
        }
        $image->imagesTags->delete();
        empty($tags) || $image->tags = $tags;
        $result = $image->save();
        if (!$result) {
            return $this->response->setJsonContent(['status' => 'error', 'data' => ['保存失败']]);
        }
        return $this->response->setJsonContent(['status' => 'success']);
    }

    public function destroyAction()
    {
        
    }
}
