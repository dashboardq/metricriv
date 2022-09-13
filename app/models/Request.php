<?php

namespace app\models;

use mavoc\core\Model;

class Request extends Model {
    public static $table = 'requests';
    public static $order = ['score' => 'desc'];

    public function updateScore() {
        $votes = Vote::where('request_id', $this->id, 'data');

        $total = 0;
        foreach($votes as $vote) {
            $total += $vote['score'];
        }
        
        $this->data['score'] = $total;
        $this->save();
    }
}
