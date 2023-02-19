    public static function {{method}}($req, $res) {
        $intervals = ['1 hour', '5 minutes', 'static'];
        $intervals = ao()->hook('app_intervals', $intervals);

        $val = $req->val('data', [
            'period' => ['required'],

            'name' => ['required'],
            'interval' => ['required', ['in' => $intervals]],
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




        ### BOF: CHOOSE ONE OF THE CONNECTION TYPES BELOW AND THEN DELETE THE REST

        // Basic connection
        $rest = new REST();
        $url = '###https://api.example.com/api/v1/users/' . $val['###username'];
        $body = $rest->get($url);
        $result = self::parseBody($body);

        if($result == -1) {
            $res->error('There was a problem accessing the data. Please confirm all the information is entered correctly. If you continue to have issues, please contact support.');
        }

        //////////////////////////////
       
        // Advanced connection
        $connection = Connection::find($req->params['connection_id']);

        $rest = new REST($connection->data['values']['api_key']);
        $url = '###https://api.example.com/api/v1/endpoint/info?username=' . $val['###username'];
        $body = $rest->get($url);
        $result = self::parseBody($body);
        if($result == -1) {
            $res->error('There was a problem accessing the API. Please confirm that your API Key and other info were entered correctly. If you continue to have issues, please contact support.');
        }

        //////////////////////////////
       
        // Advanced connection
        $connection = Connection::find($req->params['connection_id']);

        // Check that the connection is valid
        // Non-typical header
        $headers = [
            'api-key: ' . $connection->data['values']['api_key'],
        ];
        $rest = new REST($headers);
        $url = '###https://api.example.com/api/v1/endpoint/info?username=' . $val['###username'];
        $body = $rest->get($url);
        $result = self::parseBody($body);
        if($result == -1) {
            $res->error('There was a problem accessing the API. Please confirm that your API Key and other info were entered correctly. If you continue to have issues, please contact support.');
        }

        //////////////////////////////

        // Advanced connection
        $connection = Connection::find($req->params['connection_id']);

        // Check that the connection is valid
        $future_date = (date('Y') + 2) . '-01-01';
        $rest = new REST($connection->data['values']['api_key']);
        $url = '###https://api.example.com/api/v1/endpoint/info?username=' . $val['###username'] . '&period=custom&date=2000-01-01,' . $future_date . '&filter=filter';
        $body = $rest->get($url);
        $result = self::parseBody($body);
        if($result == -1) {
            $res->error('There was a problem accessing the API. Please confirm that your API Key and other info were entered correctly. If you continue to have issues, please contact support.');
        }

        //////////////////////////////

        // Advanced connection with processing
        $connection = Connection::find($req->params['connection_id']);

        // Non-typical Bearer header
        $headers = [
            'Authorization: token ' . $connection->data['values']['access']['access_token'],
            'User-Agent: ' . ao()->env('###SERVICE###_USER_AGENT'),
        ];
        $rest = new REST($headers);
        $url = '###https://api.example.com/notifications?per_page=1';
        list($headers, $body) = $rest->get($url, [], 'headers,array');
        $result = self::parseBody($headers, $body);
        if($result == -1) {
            $res->error('There was a problem accessing the API. If you continue to have issues, please contact support.');
        }


        ### EOF: CHOOSE ONE OF THE CONNECTION TYPES BELOW AND THEN DELETE THE REST





        $data = [];
        $data['###username'] = $val['###username'];
        $data['number'] = -1;
        $data['color'] = 'blue';

        $args = [];
        $args['user_id'] = $req->user_id;
        $args['number_id'] = $number->id;
        $args['collection_id'] = $req->params['collection_id'];
        ###
        //$args['connection_id'] = 0;
        //$args['connection_id'] = $connection->id;
        $args['name'] = $val['name'];
        $args['status'] = 'initial';
        $args['method'] = json_encode(['app\services\trackings\{{class_slug}}\{{class}}Service', '{{method}}Update']);
        $args['check_interval'] = $val['interval'];
        $args['next_check_at'] = new \DateTime();
        $args['data'] = $data;
        $args['encrypted'] = 0;
        $tracking = Tracking::create($args);

        TrackingService::update($tracking->id, $result);

        $res->success('You have successfully added a new number to track.', '/collection/view/' . $req->params['collection_id']);
    }
    public static function parseBody($body) {
        if(isset($body->value)) {
            return number_format($body->value);
        } else {
            return -1;
        }

        //////////////////////////////


        //////////////////////////////

        preg_match_all('|.*"followers".*?"profile-stat-num">([^<]*)</span>.*|s', $body, $matches);
        if(isset($matches[1][0])) {
            return number_format($matches[1][0]);
        } else {
            return -1;
        }
    }
    public static function {{method}}Update($tracking, $manual_result = null) {
        ### BOF: CHOOSE ONE OF THE CONNECTION TYPES BELOW AND THEN DELETE THE REST

        // Basic connection
        if($manual_result) {
            $result = $manual_result;
        } else {
            $rest = new REST();
            $url = 'https://###api.example.com/api/v1/users/' . $tracking->data['values']['###username'];
            $body = $rest->get($url);
            $result = self::parseBody($body);
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }



        //////////////////////////////

        // Advanced connection
        if($manual_result) {
            $result = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);

            $rest = new REST($connection->data['values']['api_key']);
            $url = '###https://api.example.com/api/v1/endpoint/info?username=' . $tracking->data['values']['###username'];
            $body = $rest->get($url);
            $result = self::parseBody($body);
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }

        //////////////////////////////

        // Advanced connection
        if($manual_result) {
            $result = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);

            // Check that the connection is valid
            // Non-typical header
            $headers = [
                'api-key: ' . $connection->data['values']['api_key'],
            ];
            $rest = new REST($headers);
            $url = '###https://api.example.com/api/v1/endpoint/info?username=' . $tracking->data['values']['###username'];
            $body = $rest->get($url);
            $result = self::parseBody($body);
        }

        if($result == -1) {
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }


        //////////////////////////////

        // Advanced connection
        if($manual_result) {
            $result = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);

            $future_date = (date('Y') + 2) . '-01-01';
            $rest = new REST($connection->data['values']['api_key']);
            $url = '###https://api.example.com/api/v1/endpoint/info?username=' . $tracking->data['values']['###username'] . '&period=custom&date=2000-01-01,' . $future_date . '&filter=filter';
            $body = $rest->get($url);
            $result = self::parseBody($body);
        }

        if($result == -1) {
            // Handle failures
            $tracking->failData();
        } else {
            //$values = [];
            //$values['number'] = $result->results->visitors->value;
            //$values['color'] = 'black';
            $tracking->updateData($result);
        }

        //////////////////////////////

        // Advanced connection with processing
        if($manual_result) {
            $total = $manual_result;
        } else {
            $connection = Connection::find($tracking->data['connection_id']);

            // Non-typical Bearer header
            $headers = [
                'Authorization: token ' . $connection->data['values']['access']['access_token'],
                'User-Agent: ' . ao()->env('GITHUB_USER_AGENT'),
            ];
            $rest = new REST($headers);
            $url = 'https://api.github.com/notifications?per_page=1';
            list($headers, $body) = $rest->get($url, [], 'headers,array');
            $result = self::parseBody($headers, $body);
        }

        if($result == -1) {
            // Handle failures
            $tracking->failData();
        } else {
            $tracking->updateData($result);
        }

        ### EOF: CHOOSE ONE OF THE CONNECTION TYPES BELOW AND THEN DELETE THE REST
    }

