<?php

namespace app\controllers;

use app\models\Setting;

use DateTimeZone;

class SettingController {
    public function index($req, $res) {
        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

        $days = [];
        $days[] = 'Sunday';
        $days[] = 'Monday';
        $days[] = 'Tuesday';
        $days[] = 'Wednesday';
        $days[] = 'Thursday';
        $days[] = 'Friday';
        $days[] = 'Saturday';

        $settings = Setting::get();
        $res->fields = $settings;

        $res->view('settings/index', compact('days', 'timezones'));
    }

    public function update($req, $res) {
        $val = $req->val('data', [
            'timezone' => ['required'],
            'week_start' => ['required'],
        ]);

        $timezone = Setting::set($req->user_id, $val);

        $res->success('Items updated successfully.', '/settings');
    }
}
