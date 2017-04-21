<?php

use ImsLabels as Labels;

class ImsLabelsController extends ImsBaseController
{

    public function indexAction()
    {
        // 按照Type分组返回所有日期
        $type = $this->request->get('type');
        $tags = Labels::find();
        return $this->response->setJsonContent($tags->toArray());
    }
}
