<?php

namespace app\controllers;

use app\models\Access;
use app\models\Follow;
use app\models\Collection;
use app\models\Number;
use app\models\Tracking;
use app\models\Username;

class AjaxController {
    public function collectionSort($req, $res) {
        $params = $req->val('params', [
            'id' => ['required', ['dbOwner' => ['collections', 'id', $req->user_id]]],
        ]);

        $data = $req->val('data', [
            'ids' => ['required'],
        ]);

        $ids = explode(',', $data['ids']);

        foreach($ids as $i => $id) {
            $tracking = Tracking::find($id);
            $tracking->data['priority'] = $i;
            $tracking->save();
        }

        return ['status' => 'success'];
    }
}
