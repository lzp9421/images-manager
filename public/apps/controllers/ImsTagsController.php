<?php

use ImsTags as Tags;

class ImsTagsController extends ImsBaseController
{

    public function indexAction()
    {
        // 按照Type分组返回所有日期
        $type = $this->request->get('type');
        $tags = Tags::find([
            'conditions' => 'type=?1',
            'bind' => [
                1 => $type,
            ],
        ]);
        $this->response->setJsonContent($tags);
        return $this->response;
    }
}
