<?php

namespace Multiple\Frontend\Models;

use Phalcon\Mvc\Model;

class Images extends Model
{

    public function initialize()
    {

        $this->belongsTo(
            "game_id",
            "Multiple\\Frontend\\Models\\Games",
            "id",
            [
                "alias" => "Game",
            ]
        );
        $this->hasManyToMany(
            "id",
            "Multiple\\Frontend\\Models\\ImagesTags",
            "image_id", "tag_id",
            "Multiple\\Frontend\\Models\\Tags",
            "id",
            [
                "alias" => "Tags",
            ]
        );
    }

}
