<?php
/**
 * Created by PhpStorm.
 * User: lizhipeng
 * Date: 2017/4/7
 * Time: 下午4:42
 */

use Phalcon\Mvc\Model;

class ImsLabels extends Model
{
    public function initialize()
    {
        $this->hasManyToMany(
            "id",
            "ImsGamesLabels",
            "label_id", "game_id",
            "ImsGames",
            "id",
            [
                "alias" => "Games",
            ]
        );
        $this->hasMany(
            'id',
            'ImsGamesLabels',
            'label_id',
            [
                'alias' => 'GamesLabels',
            ]
        );
    }
}