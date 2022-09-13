<?php

namespace app\controllers;

class DevController {
    public function keys($req, $res) {
        header('Content-Type: text/plain');
        $keys = [];
        $keys[] = [
            'name' => 'CONNECTIONS_1',
            'value' => sodium_bin2hex(random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES)),
        ];
        $keys[] = [
            'name' => 'NUMBERS_1',
            'value' => sodium_bin2hex(random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES)),
        ];

        $res->view('dev/keys', compact('keys'));
    }
}
