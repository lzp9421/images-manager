<?php
/**
 * Created by PhpStorm.
 * User: lizhipeng
 * Date: 2017/4/7
 * Time: 下午4:42
 */

namespace Multiple\Frontend\Models;

use Phalcon\Mvc\Model;

class Tags extends Model
{
    public function initialize()
    {
        $this->hasMany(
            "id",
            "Multiple\\Frontend\\Models\\ImagesTags",
            "tag_id",
            [
                "alias" => "ImagesTags",
            ]
        );
        $this->hasManyToMany(
            "id",
            "Multiple\\Frontend\\Models\\ImagesTags",
            "tag_id", "image_id",
            "Multiple\\Frontend\\Models\\Images",
            "id",
            [
                "alias" => "Images",
            ]
        );
    }
}