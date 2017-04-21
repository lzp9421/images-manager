<?php
/**
 * Created by PhpStorm.
 * User: lizhipeng
 * Date: 2017/4/7
 * Time: 下午4:43
 */

use Phalcon\Mvc\Model;

class ImsGames extends Model
{
    public function initialize()
    {
        $this->hasMany(
            'id',
            'ImsImages',
            'game_id',
            [
                'alias' => 'Images',
            ]
        );
        //
        $this->hasManyToMany(
            'id',
            'ImsGamesLabels',
            'game_id', 'label_id',
            'ImsLabels',
            'id',
            [
                'alias' => 'Labels',
            ]
        );
        $this->hasMany(
            'id',
            'ImsGamesLabels',
            'game_id',
            [
                'alias' => 'GamesLabels',
            ]
        );
        //
        $this->hasManyToMany(
            'id',
            'ImsGamesTags',
            'game_id', 'tag_id',
            'ImsTags',
            'id',
            [
                'alias' => 'Tags',
            ]
        );
        $this->hasMany(
            'id',
            'ImsGamesTags',
            'game_id',
            [
                'alias' => 'GamesTags',
            ]
        );
    }

}