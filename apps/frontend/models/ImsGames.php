<?php
/**
 * Created by PhpStorm.
 * User: lizhipeng
 * Date: 2017/4/7
 * Time: 下午4:43
 */

namespace Multiple\Frontend\Models;

use Phalcon\Mvc\Model;

class ImsGames extends Model
{
    public function initialize()
    {
        $this->hasMany(
            "id",
            "Multiple\\Frontend\\Models\\ImsImages",
            "game_id",
            [
                "alias" => "Images",
            ]
        );
    }

}