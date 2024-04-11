<?php

namespace mavoc\core;

class Validators {
    public function __construct() {
        ao()->hook('ao_validator_init', $this);
    }

    // Dynamic rules: 
    // https://stackoverflow.com/questions/7026487/how-to-add-methods-dynamically
    public function __call($name, $arguments) {
        return call_user_func_array($this->{$name}, $arguments);
    }

    public function _add($name, $method) {
        $this->{$name} = $method;
    }

    public function array($input, $field) {
        if(
            !isset($input[$field]) 
            || $input[$field] == ''
            || is_array($input[$field])
        ) {
            return true;
        }   

        return false;
    }   
    public function arrayMessage($input, $field) {
        $output = 'The ' . $field . ' field needs to be set to true or false.';
        return $output;
    }

    public function boolean($input, $field) {
        if(
            !isset($input[$field]) 
            || $input[$field] == ''
            || $input[$field] == 1
            || $input[$field] == 0
            || $input[$field] == 'yes'
            || $input[$field] == 'no'
            || $input[$field] == 'true'
            || $input[$field] == 'false'
        ) {
            return true;
        }   

        return false;
    }   
    public function booleanMessage($input, $field) {
        $output = 'The ' . $field . ' field needs to be set to true or false.';
        return $output;
    }

    public function dbAccessList() {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $table = $args[2];

        $by_field = $args[3];
        $user_id_value = $args[4];
        if(isset($args[5])) {
            $list_field = $args[5]; 
        } else {
            $list_field = 'user_ids'; 
        }

        $value = $input[$field];
        //echo '<pre>'; print_r(get_defined_vars());die;

        $pass = false;
        // UNSAFE: Be careful
        $results = ao()->db->query('SELECT * FROM ' . $table . ' WHERE ' . $by_field . ' = ? LIMIT 1', $value);
        if(count($results)) {
            $result = $results[0];
            if(isset($result[$list_field])) {
                if($result[$list_field]) {
                    $parts = explode(',', $result[$list_field]);
                    if(in_array($user_id_value, $parts)) {
                        $pass = true;
                    }
                } else {
                    // If the $list_field is empty, it is open to everyone and should pass.
                    $pass = true;
                }
            }
        }

        return $pass;
    }   
    public function dbAccessListMessage($input, $field) {
        $output = 'The ' . $field . ' item needs to be able to be accessed by the current user.';
        return $output;
    }


    // WARNING: This is unsafe right now. Only pass in safe values (not values from users).
    // TODO: Need to create a list of valid table and column names.
    public function dbExists() {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $table = $args[2];
        $field_name = $args[3];
        $value = $input[$field];

        if($field_name) {
            // UNSAFE: Be careful
            $results = ao()->db->query('SELECT * FROM ' . $table . ' WHERE ' . $field_name . ' = ? LIMIT 1', $value);
        } else {
            // UNSAFE: Be careful
            $results = ao()->db->query('SELECT * FROM ' . $table . ' WHERE ' . $field . ' = ? LIMIT 1', $value);
        }

        if(count($results) == 0) {
            return false;
        } else {
            return true;
        }
    }   
    public function dbExistsMessage($input, $field) {
        $output = 'The ' . $field . ' field does not exist. Please enter a value found in the database.';
        return $output;
    }

    // WARNING: This is unsafe right now. Only pass in safe values (not values from users).
    // TODO: Need to create a list of valid table and column names.
    // Needs to be used on an id field.
    public function dbOwner() {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $table = $args[2];
        $id_field = $args[3];
        $user_id_value = $args[4];
        if(isset($args[5])) {
            $user_id_field = $args[5]; 
        } else {
            $user_id_field = ''; 
        }
        $value = $input[$field];

        $results = [];

        if($user_id_field) {
            // UNSAFE: Be careful
            $results = ao()->db->query('SELECT * FROM ' . $table . ' WHERE ' . $id_field . ' = ? AND ' . $user_id_field . ' = ? LIMIT 1', $value, $user_id_value);
        } else {
            // UNSAFE: Be careful
            $results = ao()->db->query('SELECT * FROM ' . $table . ' WHERE ' . $id_field . ' = ? AND user_id = ? LIMIT 1', $value, $user_id_value);
        }

        if(count($results) == 1) {
            return true;
        } else {
            return false;
        }
    }   
    public function dbOwnerMessage($input, $field) {
        $output = 'The ' . $field . ' item needs to be owned by the current user.';
        return $output;
    }

    // WARNING: This is unsafe right now. Only pass in safe values (not values from users).
    // TODO: Need to create a list of valid table and column names.
    public function dbUnique() {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $table = $args[2];

        $current_field = $args[3] ?? '';
        $current_value = $args[4] ?? '';

        $filter_field = $args[5] ?? '';
        $filter_value = $args[6] ?? '';

        $value = $input[$field];

        // UNSAFE: Be careful
        // When current_field and current_value is passed in, it ignores the passed in value and item which should be the current item (meaning it will not throw an error because the item exists).
        if($filter_field && $filter_value && $current_field && $current_value) {
            $results = ao()->db->query('SELECT * FROM ' . $table . ' WHERE ' . $field . ' = ? AND ' . $current_field . ' != ? AND ' . $filter_field . ' = ? LIMIT 1', $value, $current_value, $filter_value);
        } elseif($filter_field && $filter_value) {
            $results = ao()->db->query('SELECT * FROM ' . $table . ' WHERE ' . $field . ' = ? AND ' . $filter_field . ' = ? LIMIT 1', $value, $filter_value);
        } elseif($current_field && $current_value) {
            $results = ao()->db->query('SELECT * FROM ' . $table . ' WHERE ' . $field . ' = ? AND ' . $current_field . ' != ? LIMIT 1', $value, $current_value);
        } else {
            $results = ao()->db->query('SELECT * FROM ' . $table . ' WHERE ' . $field . ' = ? LIMIT 1', $value);
        }

        if(count($results) == 0) {
            return true;
        } else {
            return false;
        }
    }   
    public function dbUniqueMessage($input, $field) {
        $output = 'The ' . $field . ' field already exists. Please enter a unique value.';
        return $output;
    }

    public function email($input, $field) {
        $field = $input[$field] ?? '';

        if(!filter_var($field, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;                                                                                               
    }   
    public function emailMessage($input, $field) {
        $output = 'The ' . $field . ' field must be a valid email address.';
        return $output;
    }

    public function equalLength($input, $field) {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $equal = $args[2];
        $value = $input[$field] ?? '';

        if(strlen($value) != $equal) {
            return false;
        }

        return true;                                                                                               
    }   
    public function equalLengthMessage($input, $field) {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $equal = $args[2];

        $output = 'The ' . $field . ' field must be ' . pluralize($equal, 'character') . ' long.';
        return $output;
    }

    public function equalValue($input, $field) {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $equal = $args[2];
        $value = $input[$field] ?? 0;

        if($value != $equal) {
            return false;
        }

        return true;                                                                                               
    }   
    public function equalValueMessage($input, $field) {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $equal = $args[2];

        $output = 'The ' . $field . ' field must equal ' . $equal . '.';
        return $output;
    }

    // Changing from passing: 'in' => [$array]
    // To simply: 'in' => $array
    // This may cause some problems so be on the look out for issues.
    public function in($input, $field) {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        //$list = $args[2];
        $list = array_slice($args, 2);
        $value = $input[$field];

        // Separating it out like this so hooks can be added later.
        if(in_array($value, $list)) {
            return true;
        } else {
            return false;
        }
    }   
    public function inMessage($input, $field) {
        $output = 'The ' . $field . ' field must be a valid value.';
        return $output;
    }

    public function int($input, $field) {
        $field = $input[$field] ?? '';

        if(filter_var($field, FILTER_VALIDATE_INT) === false) {
            return false;
        }

        return true;                                                                                               
    }   
    public function integer($input, $field) {
        return $this->int($input, $field);
    }
    public function intMessage($input, $field) {
        $output = 'The ' . $field . ' field must be a whole number.';
        return $output;
    }
    public function integerMessage($input, $field) {
        return $this->intMessage($input, $field);
    }

    public function match() {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $regex = $args[2];
        $value = $input[$field];

        if(!preg_match($regex, $value)) {
            return false;
        }

        return true;                                                                                               
    }   
    public function matchMessage($input, $field) {
        $output = 'The ' . $field . ' field uses characters that are not allowed.';
        return $output;
    }

    public function maxLength($input, $field) {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $length = $args[2];
        $value = $input[$field] ?? '';

        if(strlen($value) > $length) {
            return false;
        }

        return true;                                                                                               
    }   
    public function maxLengthMessage($input, $field) {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $length = $args[2];

        $output = 'The ' . $field . ' field cannot be longer than ' . pluralize($length, 'character') . '.';
        return $output;
    }

    public function maxValue($input, $field) {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $max = $args[2];
        $value = $input[$field] ?? 0;

        if($value > $max) {
            return false;
        }

        return true;                                                                                               
    }   
    public function maxValueMessage($input, $field) {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $max = $args[2];

        $output = 'The ' . $field . ' field cannot be greater than ' . $max . '.';
        return $output;
    }

    public function minLength($input, $field) {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $length = $args[2];
        $value = $input[$field] ?? '';

        if(strlen($value) < $length) {
            return false;
        }

        return true;                                                                                               
    }   
    public function minLengthMessage($input, $field) {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $length = $args[2];

        $output = 'The ' . $field . ' field cannot be shorter than ' . pluralize($length, 'character') . '.';
        return $output;
    }

    public function minValue($input, $field) {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $min = $args[2];
        $value = $input[$field] ?? 0;

        if($value < $min) {
            return false;
        }

        return true;                                                                                               
    }   
    public function minValueMessage($input, $field) {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $min = $args[2];

        $output = 'The ' . $field . ' field cannot be less than ' . $min . '.';
        return $output;
    }

    // The generic message used if the rule does not have a corresponding message.
    public function message($rule, $input, $field) {
        $output = 'The ' . $field . ' field has a problem.';
        return $output;
    }

    public function notIn() {
        $args = func_get_args();
        $input = $args[0];
        $field = $args[1];
        $list = $args[2];
        $value = $input[$field];

        if(!is_array($list)) {
            $list = explode(',', $list);
        }

        if(in_array($value, $list)) {
            return false;
        }

        return true;                                                                                               
    }   
    public function notInMessage($input, $field) {
        $output = 'The ' . $field . ' field contains a value that matches a restricted list.';
        return $output;
    }

    public function numeric($input, $field) {
        $field = $input[$field] ?? '';

        if($field == '') {
            $field = 0;
        }

        if(is_numeric($field)) {
            return true;
        }

        return false;                                                                                               
    }   
    public function numericMessage($input, $field) {
        $output = 'The ' . $field . ' field must be a number.';
        return $output;
    }

    public function optional($input, $field) {
        return true;
    }   

    public function password($input, $field) {
        // Only use this for testing when you want to register users without any password limitations.
        if(ao()->env('PASSWORD_SKIP_VALIDATION')) {
            return true;
        }

        $length = ao()->hook('ao_validator_password_length', 8);
        if(!isset($input[$field]) || strlen($input[$field]) < $length) {
            return false;
        }   

        return true;
    }   
    public function passwordMessage($input, $field) {
        $output = 'The ' . $field . ' field needs to have at least 8 characters.';
        return $output;
    }
    public function required($input, $field) {
        if(!isset($input[$field]) || $input[$field] == '') {
            return false;
        }   

        return true;
    }   
    public function requiredMessage($input, $field) {
        $output = 'The ' . $field . ' field is required.';
        return $output;
    }

    public function sometimes($input, $field) {
        return true;
    }   
}
