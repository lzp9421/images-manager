<?php

namespace Multiple\Frontend\Models;

use Phalcon\Mvc\Model;

class ImsImages extends Model
{

    public function initialize()
    {

        $this->belongsTo(
            "game_id",
            "Multiple\\Frontend\\Models\\ImsGames",
            "id",
            [
                "alias" => "Game",
            ]
        );
        $this->hasMany(
            "id",
            "Multiple\\Frontend\\Models\\ImsImagesTags",
            "image_id",
            [
                "alias" => "ImagesTags",
            ]
        );
        $this->hasManyToMany(
            "id",
            "Multiple\\Frontend\\Models\\ImsImagesTags",
            "image_id", "tag_id",
            "Multiple\\Frontend\\Models\\ImsTags",
            "id",
            [
                "alias" => "Tags",
            ]
        );
    }

}
