<?php
/**
 * Created by PhpStorm.
 * User: lizhipeng
 * Date: 2017/4/7
 * Time: 下午4:40
 */

namespace Multiple\Frontend\Models;

use Phalcon\Mvc\Model;

class ImagesTags extends Model
{

    public function initialize()
    {

        $this->belongsTo(
            "image_id",
            "Multiple\\Frontend\\Models\\Images",
            "id",
            [
                "alias" => "Images",
            ]
        );

        $this->belongsTo(
            "tag_id",
            "Multiple\\Frontend\\Models\\Tags",
            "id",
            [
                "alias" => "Tags",
            ]
        );

    }

}