<?php

namespace app\controllers;

use mavoc\core\Email;

class MainController {
    public function home($req, $res) {
        //$res->view('home');
        return ['title' => 'NumbersQ.com - Fast Dashboard For Your Most Important Business Numbers'];
    }

    public function pricing($req, $res) {
        //$res->view('pricing');
        return ['title' => 'Pricing'];
    }

    public function privacy($req, $res) {
        //$res->view('privacy');
        return ['title' => 'Privacy Policy'];
    }

    public function terms($req, $res) {
        //$res->view('terms');
        return ['title' => 'Terms of Service'];
    }

    public function contact($req, $res) {
        $res->view('contact');
    }

    public function contactPost($req, $res) {
        //$res->error('Contact form not enabled at this time.');
        $val = $req->val($req->data, [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'message' => ['required'],
        ]);

        $email = new Email();
        $email->replyTo($val['email']);
        $email->subject('Contact Form: ' . $val['email']);
        $email->message($val['message']);
        $email->send();

        $res->success('Thank you, NumbersQ is ran by one person and I do my best to respond to all legitimate messages within 24-48 hours. Thanks, Anthony Graddy');
    }
}
