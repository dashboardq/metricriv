<?php

namespace app\models;

use mavoc\core\Exception;
use mavoc\core\Model;

use DateTime;

class RefreshLogin extends Model {
    public static $table = 'refresh_logins';
    public static $private = ['refresh_hash'];

    public static function create($args) {
        // Delete old refresh hashes
        ao()->db->query('DELETE FROM refresh_logins WHERE user_id = ? AND expired_at < ?', $args['user_id'], now());

        // We don't want this to be too long otherwise we run into bcrypt 72 byte limit:
        // https://crypto.stackexchange.com/questions/24993/is-there-a-way-to-use-bcrypt-with-passwords-longer-than-72-bytes-securely
        // https://security.stackexchange.com/questions/39849/does-bcrypt-have-a-maximum-password-length
        // Create new refresh token
        $length = 16;
        $token = sodium_bin2hex(random_bytes($length));
        $args['refresh_hash'] = password_hash($token, PASSWORD_DEFAULT);

        $dt = new DateTime();
        $seconds = ao()->env('APP_SESSION_SECONDS');
        $dt->modify('+ ' . $seconds . ' seconds');
        $args['expired_at'] = $dt;

        $item = new RefreshLogin($args);
        $item->save();

        // Cookie config
        $path = '/';
        $domain = ao()->env('APP_HOST');
        $site = ao()->env('APP_SITE');
        if(preg_match('/^https.*/', $site)) {
            $secure = true;
        } else {
            $secure = false;
        }
        $httponly = true;

        // Save cookies
        setcookie('refresh_token', $token, time() + $seconds, $path, $domain, $secure, $httponly);
        setcookie('refresh_user_id', $args['user_id'], time() + $seconds, $path, $domain, $secure, $httponly);

        return $item;
    }

    public static function destroy() {
        // Cookie config
        $path = '/';
        $domain = ao()->env('APP_HOST');
        $site = ao()->env('APP_SITE');
        if(preg_match('/^https.*/', $site)) {
            $secure = true;
        } else {
            $secure = false;
        }
        $httponly = true;

        setcookie('refresh_token', '', time() - 3600, $path, $domain, $secure, $httponly);
        setcookie('refresh_user_id', '', time() - 3600, $path, $domain, $secure, $httponly);

        // Delete old refresh hashes
        ao()->db->query('DELETE FROM refresh_logins WHERE user_id = ?', ao()->session->user_id);
    }

    public static function refresh() {
        if(isset($_COOKIE['refresh_user_id']) && isset($_COOKIE['refresh_token'])) {
            $user_id = $_COOKIE['refresh_user_id'];
            $password = $_COOKIE['refresh_token'];

            $args = [];
            $args['user_id'] = $user_id;
            $args['expired_at'] = ['>', now()];
            $refresh_logins = RefreshLogin::where($args);

            $user = User::find($user_id);
            if($user) {
                foreach($refresh_logins as $refresh_login) {
                    if($refresh_login && password_verify($password, $refresh_login->all['refresh_hash'])) {
                        return ['user' => $user, 'user_id' => $user_id];
                    }
                }
            }
        }

        return false;
    }
}
