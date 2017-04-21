<?php
/**
 * Created by PhpStorm.
 * User: lizhipeng
 * Date: 2017/4/7
 * Time: 下午4:40
 */

use Phalcon\Mvc\Model;

class ImsGamesLabels extends Model
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
            'label_id',
            'ImsLabels',
            'id',
            [
                'alias' => 'Label',
            ]
        );

    }

}