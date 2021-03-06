<?php
/**
 * Created by PhpStorm.
 * User: lizhipeng
 * Date: 2017/4/7
 * Time: 下午4:42
 */

use Phalcon\Mvc\Model;

class ImsTags extends Model
{
    public function initialize()
    {
        $this->hasManyToMany(
            "id",
            "ImsImagesTags",
            "tag_id", "image_id",
            "ImsImages",
            "id",
            [
                "alias" => "Images",
            ]
        );
        $this->hasMany(
            "id",
            "ImsImagesTags",
            "tag_id",
            [
                "alias" => "ImagesTags",
            ]
        );

        //
        $this->hasManyToMany(
            'id',
            'ImsGamesTags',
            'tag_id', 'game_id',
            'ImsGames',
            'id',
            [
                'alias' => 'Games',
            ]
        );
        $this->hasMany(
            "id",
            "ImsGamesTags",
            "tag_id",
            [
                "alias" => "GamesTags",
            ]
        );
    }
}