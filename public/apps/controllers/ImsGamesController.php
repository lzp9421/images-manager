<?php

use ImsGames as Games;
use ImsLabels as Labels;
use ImsTags as Tags;

class ImsGamesController extends ImsBaseController
{

    public function indexAction()
    {
        $this->request->get('');
        // 按照Type分组返回所有日期
        $dates = Games::find([
            // 'conditions' => '',
        ]);
        return $this->response->setJsonContent($dates->toArray());
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
        return $this->response->setJsonContent($games->toArray());
    }

    /**
     * API接口
     * 添加一条赛事
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function createAction($token = 'token')
    {
        if (!password_verify($this->config->server->key, $token)) {
            return $this->response->setJsonContent(['status' => 'error', 'data' => 'unauthorized']);
        }

        $remote_game_id = $this->request->getPost('game_id', 'int');
        $name           = $this->request->getPost('name', 'string');
        $date           = $this->request->get('date');
        $type           = $this->request->getPost('type', 'string');
        $labels         = (array)$this->request->getPost('labels', 'string');
        $tags           = (array)$this->request->getPost('tags', 'string');

        $game = new Games;
        $game->remote_game_id = $remote_game_id;
        $game->name   = $name;
        $game->date   = (new \DateTime($date, new \DateTimeZone('PRC')))->format('Y-m-d');
        $game->type   = $type === '足球' ? '足球' : '篮球' ;
        $game->labels = array_map(function ($label) {
            return Labels::findFirst([
                'conditions' => 'name = ?1',
                'bind' => [
                    1 => $label,
                ],
            ]);
        }, $labels) ?: null;
        $game->tags   = array_map(function ($tag) {
            return Tags::findFirst([
                'conditions' => 'name = ?1',
                'bind' => [
                    1 => $tag,
                ],
            ]);
        }, $tags) ?: null;
        if (!$game->save()) {
            return $this->response->setJsonContent(['status' => 'error', 'data' => $game->getMessages()]);
        }
        return $this->response->setJsonContent(['status' => 'success']);
    }
}
