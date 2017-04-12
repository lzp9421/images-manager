<?php

namespace Multiple\Frontend\Controllers;

use Multiple\Frontend\Models\Games;

class GamesController extends BaseController
{

    public function indexAction()
    {
        // 按照Type分组返回所有日期
        $dates = Games::find([
            // 'conditions' => '',
        ]);
        $this->response->setJsonContent($dates);
        return $this->response;
    }

    public function getGamesAction()
    {
        $type = $this->request->get('type');
        $date = $this->request->get('date');
        // 获取指定分类和日期的所有比赛
        $games = Games::find([
            'conditions' => 'date = ?1 AND type = ?2',
            'bind' => [
                1 => $date,
                2 => $type,
            ],
            'columns' => 'id, name',
        ]);
        $this->response->setJsonContent($games);
        return $this->response;
    }
}
