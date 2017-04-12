<?php

namespace Multiple\Frontend\Controllers;

use Multiple\Frontend\Models\Games;
use Multiple\Frontend\Models\Images;

class ImagesController extends BaseController
{
    /*
     * GET	/photo	index	photo.index
GET	/photo/create	create	photo.create
POST	/photo	store	photo.store
GET	/photo/{photo}	show	photo.show
GET	/photo/{photo}/edit	edit	photo.edit
PUT/PATCH	/photo/{photo}	update	photo.update
DELETE	/photo/{photo}	destroy	photo.destroy
     */

    public function indexAction()
    {
        $game_id = $this->request->get('game_id');
        $images = Images::find([
            'conditions' => 'game_id = ?1',
            'bind' => [
                1 => $game_id,
            ],
        ]);
        $this->response->setJsonContent($images);
        return $this->response;
    }

    public function createAction()
    {
        
    }

    public function storeAction()
    {
        
    }

    public function showAction()
    {
        
    }

    public function editAction()
    {
        
    }

    public function updateAction()
    {
        
    }

    public function destroyAction()
    {
        
    }
}
