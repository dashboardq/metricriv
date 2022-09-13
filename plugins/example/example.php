<?php

namespace plugins\example;

class Example {
    public function __construct() {
        ao()->filter('ao_example_hook', [$this, 'exclaim']);
    }

    public function exclaim($input) {
        $output = $input . '!';
        return $output;
    }
}
