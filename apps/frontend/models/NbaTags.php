<?php
/**
 * Created by PhpStorm.
 * User: lizhipeng
 * Date: 2017/4/7
 * Time: 下午4:42
 */

namespace Multiple\Frontend\Models;

use Phalcon\Mvc\Model;

class NbaTags extends Model
{
    public function initialize()
    {
        $this->hasManyToMany(
            "id",
            "Multiple\\Frontend\\Models\\NbaImagesTags",
            "tag_id", "image_id",
            "Multiple\\Frontend\\Models\\NbaImages",
            "id",
            [
                "alias" => "Images",
            ]
        );
    }
}