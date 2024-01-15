<?php

namespace app\services\extras;

use app\models\Tracking;

class MetricCalculationsExtraService {
    public static function main($req, $res, $category, $number, $connection) {
        $decimals = self::decimals();
        $formats = self::formats();
        $number_1 = [];
        $number_2 = [];
        $operations = self::operations();
        //$roundings = self::roundings();

        $number_1 = self::trackings($req->params['collection_id'], 'select');
        $number_2 = self::trackings($req->params['collection_id'], 'select');

        return compact('number_1', 'operations', 'number_2', 'formats', 'decimals');
    }

    public static function decimals() {
        $output = [];

        $output[] = 'None';
        $output[] = '1';
        $output[] = '2';
        $output[] = '3';
        $output[] = '4';
        $output[] = '5';
        $output[] = '6';
        $output[] = '7';
        $output[] = '8';
        $output[] = '9';

        return $output;
    }

    public static function formats() {
        $output = [];

        $output[] = 'Plain';
        $output[] = 'Percent';
        $output[] = 'Money';

        return $output;
    }

    public static function operations() {
        $output = [];

        $output[] = '+';
        $output[] = '-';
        $output[] = 'x';
        $output[] = '/';

        return $output;
    }

    public static function roundings() {
        $output = [];

        $output[] = 'Auto';
        $output[] = 'Round Up';
        $output[] = 'Round Down';
        $output[] = 'Truncate';

        return $output;
    }

    public static function trackings($collection_id, $type = 'select') {
        $trackings = Tracking::where('collection_id', $collection_id, 'data');
        $output = [];
        foreach($trackings as $tracking) {
            // Skip non-number types.
            if(
                isset($tracking['values']['type']) 
                && in_array($tracking['values']['type'], ['header', 'newline', 'hide'])
            ) {
                continue;
            }

            if($type == 'id') {
                $output[] = $tracking['id'];
            } else {
                $output[] = [
                    'label' => $tracking['title'] . ' - ' . $tracking['category']['name'] . ' - ' . $tracking['number']['short_name'],
                    'value' => $tracking['id'],
                ];
            }
        }
        return $output;
    }
}
