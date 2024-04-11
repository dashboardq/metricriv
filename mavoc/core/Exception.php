<?php

namespace mavoc\core;

use Exception as OriginalException;

class Exception extends OriginalException {
    public $response_type = 'html';
    public $redirect = '';

    public function __construct($message = '', $redirect = '', $code = 302, $response_type = 'html', Throwable $previous = null) {
        $this->redirect = $redirect;
        $this->response_type = $response_type;
        parent::__construct($message, $code, $previous);
    }

    public function getRedirect() {
        return $this->redirect;
    }

    public function getResponseType() {
        return $this->response_type;
    }
}

