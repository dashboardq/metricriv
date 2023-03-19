<?php

namespace app\services\trackings\numbersq_calculations;

use app\services\TrackingService;

use app\models\Category;
use app\models\Collection;
use app\models\Connection;
use app\models\Number;
use app\models\Setting;
use app\models\Tracking;

use mavoc\core\REST;

use DateTime;
use DateTimeZone;

class NumbersQCalculationsCurrentDayService {
        public static function currentDay($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $periods = ['week', 'month', 'year'];
        $val = $req->val('data', [
            'period' => ['required', ['in' => $periods]],

            'name' => ['required'],
            'interval' => ['required', ['in' => $intervals]],
            'priority' => ['required', 'int'],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);

        $result = self::parseCalculation($req->user_id, $val['period']);

        $data = [];
        $data['period'] = $val['period'];
        // Used because the directory has a dash which cannot be processed by the TrackingService::update() method
        $data['file'] = 'app/services/trackings/numbersq-calculations/NumbersQCalculationsCurrentDayService.php';
        $data['number'] = -1;
        $data['color'] = 'blue';

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        $args['connection_id'] = 0;
        $args['name'] = $val['name'];
        $args['status'] = 'initial';
        $args['method'] = json_encode(['app\services\trackings\numbersq_calculations\NumbersQCalculationsCurrentDayService', 'currentDayUpdate']);
        $args['check_interval'] = $val['interval'];
        $args['priority'] = $val['priority'];
        $args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        $collection = Collection::find($req->params['collection_id']);
        $collection->resort();

        TrackingService::update($tracking->id, $result);

        $res->success('You have successfully added a new number to track.', '/collection/view/' . $req->params['collection_id']);
    }
    public static function parseCalculation($user_id, $period) {
        $output = -1;

        $timezone = Setting::get($user_id, 'timezone');
        $week_start = Setting::get($user_id, 'week_start');

        $days = [];
        $days['Sunday'] = 1;
        $days['Monday'] = 2;
        $days['Tuesday'] = 3;
        $days['Wednesday'] = 4;
        $days['Thursday'] = 5;
        $days['Friday'] = 6;
        $days['Saturday'] = 7;

        // There is probably a better way to do this but this works for now.
        $days_offset = [];
        $days_offset['Sunday'] = (((-1 * $days[$week_start]) + 8) % 7) + 1;
        $days_offset['Monday'] = (((-1 * $days[$week_start]) + 9) % 7) + 1;
        $days_offset['Tuesday'] = (((-1 * $days[$week_start]) + 10) % 7) + 1;
        $days_offset['Wednesday'] = (((-1 * $days[$week_start]) + 11) % 7) + 1;
        $days_offset['Thursday'] = (((-1 * $days[$week_start]) + 12) % 7) + 1;
        $days_offset['Friday'] = (((-1 * $days[$week_start]) + 13) % 7) + 1;
        $days_offset['Saturday'] = (((-1 * $days[$week_start]) + 14) % 7) + 1;
            
        $tz = new DateTimeZone($timezone);
        $dt = new DateTime('now', $tz);

        if($period == 'week') {
            $today = $dt->format('l');
            $output = $days_offset[$today];
        } elseif($period == 'month') {
            $output = $dt->format('j');
        } elseif($period == 'year') {
            $output = $dt->format('z') + 1;
        }

        return $output;
    }
    public static function currentDayUpdate($tracking, $manual_result = null) {
        if($manual_result) {
            $result = $manual_result;
        } else {
            $result = self::parseCalculation($tracking->data['user_id'], $tracking->data['values']['period']);
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }
    }


}
