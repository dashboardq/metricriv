<?php

namespace app\services\trackings\numbersq_calculations;

use app\services\TrackingService;

use app\models\Category;
use app\models\Collection;
use app\models\Connection;
use app\models\Number;
use app\models\Tracking;

use app\services\extras\NumbersQCalculationsExtraService;

use mavoc\core\REST;

use DateTime;

class NumbersQCalculationsMathOperationService {
    public static function mathOperation($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $trackings = NumbersQCalculationsExtraService::trackings($req->params['collection_id'], 'id');
        $operations = NumbersQCalculationsExtraService::operations();
        $decimals = NumbersQCalculationsExtraService::decimals();
        $formats = NumbersQCalculationsExtraService::formats();

        $val = $req->val('data', [
            'number_1' => ['required', ['in' => $trackings]],
            'number_2' => ['required', ['in' => $trackings]],
            'operation' => ['required', ['in' => $operations]],
            'decimal' => ['required', ['in' => $decimals]],
            'format' => ['required', ['in' => $formats]],

            'name' => ['required'],
            'interval' => ['required', ['in' => $intervals]],
            'priority' => ['required', 'int'],
        ]);

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);


        $result = self::parseCalculation($val['number_1'], $val['number_2'], $val['operation'], $val['format'], $val['decimal']);


        $data = [];
        $data['type'] = 'calculation';
        $data['tracking_id_1'] = $val['number_1'];
        $data['tracking_id_2'] = $val['number_2'];
        $data['operation'] = $val['operation'];
        $data['decimal'] = $val['decimal'];
        $data['format'] = $val['format'];
        // Used because the directory has a dash which cannot be processed by the TrackingService::update() method
        $data['file'] = 'app/services/trackings/numbersq-calculations/NumbersQCalculationsMathOperationService.php';
        $data['number'] = -1;
        $data['color'] = 'blue';

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        $args['connection_id'] = 0;
        $args['name'] = $val['name'];
        $args['status'] = 'initial';
        $args['method'] = json_encode(['app\services\trackings\numbersq_calculations\NumbersQCalculationsMathOperationService', 'mathOperationUpdate']);
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
    public static function parseCalculation($tracking_id_1, $tracking_id_2, $operation, $format, $decimal) {
        $value = -1;

        // Force each tracking item to update
        // If the tracking ids are the same, only run the update once.
        if($tracking_id_1 == $tracking_id_2) {
            TrackingService::update($tracking_id_1, null, true);
        } else {
            TrackingService::update($tracking_id_1, null, true);
            TrackingService::update($tracking_id_2, null, true);
        }

        $tracking_1 = Tracking::find($tracking_id_1);
        $tracking_2 = Tracking::find($tracking_id_2);

        // Strip values of non-numerical characters
        if($tracking_1 && isset($tracking_1->data['values']['number'])) {
            $value_1 = filter_var($tracking_1->data['values']['number'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        } else {
            // One of the values is missing a number.
            return -1;
        }

        if($tracking_2 && isset($tracking_2->data['values']['number'])) {
            $value_2 = filter_var($tracking_2->data['values']['number'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        } else {
            // One of the values is missing a number.
            return -1;
        }

        if($value_1 == -1 || $value_2 == -1) {
            return -1;
        }

        // Perform the calculation
        if($operation == '+') {
            $value = $value_1 + $value_2;
        } elseif($operation == '-') {
            $value = $value_1 - $value_2;
        } elseif($operation == 'x') {
            $value = $value_1 * $value_2;
        } elseif($operation == '/') {
            $value = $value_1 / $value_2;
        }

        // Perform any format calculations
        if($format == 'Percent') {
            $value = $value * 100;
        }

        // Format the final number
        if($format == 'Percent') {
            if($decimal == 'None') {
                $value = number_format($value) . '%';
            } else {
                $value = number_format($value, intval($decimal)) . '%';
            }
        } elseif($format == 'Money') {
            if($decimal == 'None') {
                $value = '$' . number_format($value);
            } else {
                $value = '$' . number_format($value, intval($decimal));
            }
        } else {
            if($decimal == 'None') {
                $value = number_format($value);
            } else {
                $value = number_format($value, intval($decimal));
            }
        }

        return $value;
    }
    public static function mathOperationUpdate($tracking, $manual_result = null) {
       // Basic connection
        if($manual_result) {
            $result = $manual_result;
        } else {
            $result = self::parseCalculation(
                $tracking->data['values']['tracking_id_1'],
                $tracking->data['values']['tracking_id_2'],
                $tracking->data['values']['operation'],
                $tracking->data['values']['format'],
                $tracking->data['values']['decimal']
            );
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }
    }


}
