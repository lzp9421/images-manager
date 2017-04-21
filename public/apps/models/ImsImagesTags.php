<?php
/**
 * Created by PhpStorm.
 * User: lizhipeng
 * Date: 2017/4/7
 * Time: 下午4:40
 */

use Phalcon\Mvc\Model;

class ImsImagesTags extends Model
{

    public function initialize()
    {

        $this->belongsTo(
            "image_id",
            "ImsImages",
            "id",
            [
                "alias" => "Images",
            ]
        );

        $this->belongsTo(
            "tag_id",
            "ImsTags",
            "id",
            [
                "alias" => "Tags",
            ]
        );

    }

}