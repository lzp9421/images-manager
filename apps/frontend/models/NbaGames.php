<?php
/**
 * Created by PhpStorm.
 * User: lizhipeng
 * Date: 2017/4/7
 * Time: 下午4:43
 */

namespace Multiple\Frontend\Models;

use Phalcon\Mvc\Model;

class NbaGames extends Model
{
    public function initialize()
    {
        $this->hasMany(
            "id",
            "Multiple\\Frontend\\Models\\NbaImages",
            "game_id",
            [
                "alias" => "Images",
            ]
        );
    }

}