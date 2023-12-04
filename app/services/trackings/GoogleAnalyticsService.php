<?php

namespace app\services\trackings;

use app\services\TrackingService;

use app\models\Category;
use app\models\Collection;
use app\models\Connection;
use app\models\Number;
use app\models\Tracking;

use mavoc\core\REST;

use DateTime;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;

class GoogleAnalyticsService {


    public static function activeUsers($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'period' => ['required'],

            'name' => ['required'],
            'interval' => ['required', ['in' => $intervals]],
            'priority' => ['required', 'int'],
        ]);

        // If other, the other values are required
        if($val['period'] == 'other') {
            $other = $req->val('data', [
                'years_ago' => ['required', 'int'],
                'months_ago' => ['required', 'int'],
                'weeks_ago' => ['required', 'int'],
                'days_ago' => ['required', 'int'],

                'years_range' => ['required', 'int'],
                'months_range' => ['required', 'int'],
                'weeks_range' => ['required', 'int'],
                'days_range' => ['required', 'int'],
            ]);
        }

        $category = Category::by('slug', $req->params['category_slug']);
        $number = Number::by([
            'slug' => $req->params['number_slug'],
            'category_id' => $category->id,
        ]);

        $range = 'all';
        $ago = 'now';
        if($val['period'] == 'other') {
            $range = '';
            $range .= $other['years_range'] . 'y';
            $range .= $other['months_range'] . 'm';
            $range .= $other['weeks_range'] . 'w';
            $range .= $other['days_range'] . 'd';

            $ago = '';
            $ago .= $other['years_ago'] . 'y';
            $ago .= $other['months_ago'] . 'm';
            $ago .= $other['weeks_ago'] . 'w';
            $ago .= $other['days_ago'] . 'd';
        } elseif(strpos($val['period'], '_') !== false) {
            $parts = explode('_', $val['period']);
            if(count($parts) == 2) {
                $range = $parts[0];
                $ago = $parts[1];
            }
        }

       
        // Advanced connection
        try {
            putenv(ao()->env('GOOGLE_ANALYTICS_CONFIG'));
            $client = new BetaAnalyticsDataClient();

            $connection = Connection::find($req->params['connection_id']);
            $collection = Collection::find($req->params['collection_id']);

            $property_id = $connection->data['values']['api_key'];
            $args = GoogleAnalyticsService::parsePeriod($collection->data['user_id'], $property_id, $range, $ago);

            $response = $client->runReport($args);
            $result = self::activeUsersParse($response);
            if($result == -1) {
                $res->error('There was a problem accessing the API. Please check that your Property ID and other info were entered correctly. If you continue to have issues, please contact support.');
            }
        } catch (\Exception $e) {
            //$res->error('There was a problem accessing the API. Please confirm that your Property ID and other info were entered correctly. If you continue to have issues, please contact support.');
            $res->error($e->getMessage());
        }

        $data = [];
        $data['range'] = $range;
        $data['ago'] = $ago;
        $data['number'] = -1;
        $data['color'] = 'blue';

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        $args['connection_id'] = $connection->id;
        $args['name'] = $val['name'];
        $args['status'] = 'initial';
        $args['method'] = json_encode(['app\services\trackings\GoogleAnalyticsService', 'activeUsersUpdate']);
        $args['check_interval'] = $val['interval'];
        $args['priority'] = $val['priority'];
        $args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        TrackingService::update($tracking->id, $result);

        $res->success('You have successfully added a new number to track.', '/collection/view/' . $req->params['collection_id']);
    }

    public static function activeUsersParse($response) {
        $out = '';
        try {
            $found = false;
            $temp = 0;
            $out .= 'Dev Testing: Before foreach ';
            $out .= "\n";
            foreach ($response->getRows() as $row) {
                $out .= 'Loop';
                $out .= "\n";
                $temp += $row->getMetricValues()[0]->getValue();
                //error_log($row->getDimensionValues()[0]->getValue() . ': ' . $row->getMetricValues()[0]->getValue());
                //error_log('temp: ' . $temp);
                $found = true;
            }
            if($found) {
                $out .= 'Found';
                $out .= "\n";
                $value = $temp;
            }
            if(isset($value)) {
                return number_format($value);
            } else {
                // No results and no errors so 0.
                return 0;
            }
        } catch(\Exception $e) {
            return -1;
        }
    }

    public static function activeUsersUpdate($tracking, $manual_result = null) {
        if($manual_result) {
            $result = $manual_result;
        } else {
            try {
                putenv(ao()->env('GOOGLE_ANALYTICS_CONFIG'));
                $client = new BetaAnalyticsDataClient();

                $connection = Connection::find($tracking->data['connection_id']);
                $property_id = $connection->data['values']['api_key'];

                $dt = new DateTime();
                $today = $dt->format('Y-m-d');

                $args = GoogleAnalyticsService::parsePeriod($tracking->data['collection']['user_id'], $property_id, $tracking->data['values']['range'], $tracking->data['values']['ago']);
                $response = $client->runReport($args);
                $result = self::activeUsersParse($response);
            } catch (\Exception $e) {
                $result = -1;
            }
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }
    }

    public static function parsePeriod($user_id, $property_id, $range, $ago) {
        // Ignore the $user_id timezone because we don't need to adjust for Google Analytics.
        //$dates = ao()->app->getDates($user_id, $range, $ago, 'Y-m-d');
        //$dates = ao()->app->getDates(0, $range, $ago, 'Y-m-d');
        $dates = ao()->app->getDates($user_id, $range, $ago, 'Y-m-d', $user_id);
        //ao()->response->error('There was a problem parsing dates. Please contact support. ' . print_r($dates, true));

        $args = [
            'property' => 'properties/' . $property_id,
            'dateRanges' => [
                new DateRange([
                    'start_date' => $dates['start'],
                    'end_date' => $dates['end'],
                ]),
            ],
            'dimensions' => [new Dimension(
                [
                    'name' => 'date',
                ]
            ),
            ],
            'metrics' => [new Metric(
                [
                    'name' => 'activeUsers',
                ]
            )
            ]
        ];

        return $args;
    }

}
