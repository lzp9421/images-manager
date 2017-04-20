<?php
/**
 * Created by PhpStorm.
 * User: lizhipeng
 * Date: 2017/4/7
 * Time: 下午4:40
 */

namespace Multiple\Frontend\Models;

use Phalcon\Mvc\Model;

class ImsImagesTags extends Model
{

    public function initialize()
    {

        $this->belongsTo(
            "image_id",
            "Multiple\\Frontend\\Models\\ImsImages",
            "id",
            [
                "alias" => "Images",
            ]
        );

        $this->belongsTo(
            "tag_id",
            "Multiple\\Frontend\\Models\\ImsTags",
            "id",
            [
                "alias" => "Tags",
            ]
        );

    }

}