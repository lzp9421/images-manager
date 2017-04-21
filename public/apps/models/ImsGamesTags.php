<?php
/**
 * Created by PhpStorm.
 * User: lizhipeng
 * Date: 2017/4/7
 * Time: 下午4:40
 */

use Phalcon\Mvc\Model;

class ImsGamesTags extends Model
{

    public function initialize()
    {

        $this->belongsTo(
            'game_id',
            'ImsGames',
            'id',
            [
                'alias' => 'Game',
            ]
        );

        $this->belongsTo(
            'tag_id',
            'ImsTags',
            'id',
            [
                'alias' => 'Tag',
            ]
        );

    }

}