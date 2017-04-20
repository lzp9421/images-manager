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
        return $this->response->setJsonContent($dates);
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

    /**
     * API接口
     * 添加一条赛事
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function createAction()
    {
        $date = $this->request->get('date');
        $name = $this->request->get('name', 'string');
        $type = $this->request->get('type', 'string');
        $game = new Games;
        $game->name = $name;
        $game->date = (new \DateTime($date, new \DateTimeZone('PRC')))->format('Y-m-d');
        $game->type = $type === '足球' ? '足球' : '篮球' ;
        if (!$game->save()) {
            return $this->response->setJsonContent(['status' => 'error', 'data' => $game->getMessages()]);
        }
        return $this->response->setJsonContent(['status' => 'success']);
    }
}
