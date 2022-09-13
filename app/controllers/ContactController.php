<?php

namespace app\controllers;

use mavoc\core\Email;

class ContactController {
    public function contact($req, $res) {
        //$res->view('contact');
        return ['title' => 'Contact'];
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

        $res->success('Thank you, your message has been sent.');
    }
}
