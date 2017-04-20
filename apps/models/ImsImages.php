<?php

use Phalcon\Mvc\Model;

class ImsImages extends Model
{

    public function initialize()
    {

        $this->belongsTo(
            "game_id",
            "ImsGames",
            "id",
            [
                "alias" => "Game",
            ]
        );
        $this->hasMany(
            "id",
            "ImsImagesTags",
            "image_id",
            [
                "alias" => "ImagesTags",
            ]
        );
        $this->hasManyToMany(
            "id",
            "ImsImagesTags",
            "image_id", "tag_id",
            "ImsTags",
            "id",
            [
                "alias" => "Tags",
            ]
        );
    }

}
