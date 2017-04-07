<?php

namespace Multiple\Frontend\Models;

use Phalcon\Mvc\Model;

class NbaImages extends Model
{

    public function initialize()
    {

        $this->belongsTo(
            "game_id",
            "Multiple\\Frontend\\Models\\NbaGames",
            "id",
            [
                "alias" => "Game",
            ]
        );
        $this->hasManyToMany(
            "id",
            "Multiple\\Frontend\\Models\\NbaImagesTags",
            "image_id", "tag_id",
            "Multiple\\Frontend\\Models\\NbaTags",
            "id",
            [
                "alias" => "Tags",
            ]
        );
    }

}
