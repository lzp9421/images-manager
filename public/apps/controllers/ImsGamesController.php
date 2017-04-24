<?php

use ImsGames as Games;
use ImsLabels as Labels;
use ImsTags as Tags;

class ImsGamesController extends ImsBaseController
{

    public function indexAction()
    {
        $labels_name = (array)$this->request->get('labels', 'string');
        // 按照Type分组返回所有日期
        if (empty($labels_name)) {
            $games = Games::find();
        } else {
            $labels = Labels::find([
                'conditions' => 'name IN({labels_name:array})',
                'bind' => [
                    'labels_name' => $labels_name,
                ],
            ]);
            $ids = [];
            foreach ($labels as $label) {
                $ids = array_unique($ids + array_map(function ($game) {
                        return $game['id'];
                    }, $label->games->toArray()));
            }
            $games = Games::find([
                'conditions' => 'id IN({ids:array})',
                'bind' => [
                    'ids' => $ids,
                ],
            ]);
        }
        return $this->response->setJsonContent($games->toArray());
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
    public function createAction()
    {
        $secret    = $this->request->get('secret');
        $timestamp = $this->request->get('timestamp');
        if (!api_verify($secret, $timestamp, $this->config->server->key)) {
            return $this->response->setJsonContent(['status' => 'error', 'data' => '']);
        }

        $remote_game_id = $this->request->getPost('game_id', 'int');
        $name           = $this->request->getPost('name', 'string');
        $date           = $this->request->getPost('date');
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
