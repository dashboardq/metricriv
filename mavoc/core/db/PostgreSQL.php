<?php

namespace mavoc\core\db;

use PDO;

class PostgreSQL {
    public static function createTable($table, $args) {
        $sql = '';

        $field_count = 0;
        $primary_key = '';

        $sql = '';
        // TODO: Be careful. Do not use with user passed in data. Need to prepare the table passed in.
        $sql .= 'CREATE TABLE "' . $table . '" ( ';
        foreach($args as $key => $arg) {
            if(is_string($arg)) {
                if($field_count) {
                    $sql .= ', ';
                }

                // Save the first $primary_key
                if($arg == 'id' && !$primary_key) {
                    $primary_key = $key;
                }

                $sql .= self::createType($key, $arg, $key == $primary_key);

                $field_count++;
            } elseif(is_array($arg) && isset($arg['type'])) {
                if($field_count) {
                    $sql .= ', ';
                }

                // Save the first $primary_key
                if($arg == 'id' && !$primary_key) {
                    $primary_key = $key;
                }

                $sql .= self::createType($key, $arg['type'], $arg, $key == $primary_key);

                $field_count++;
            }
        }

        /* Primary key is being added on the type itself, not needed here.
        if($primary_key) {
            $sql .= ', PRIMARY KEY ("' . $primary_key . '")';
        }
         */

        $sql .= ' )';

        return $sql;
    }

    public static function createType($key, $type = '', $primary_key = false, $extras = []) {
        $sql = '';

        if($type == 'id') {
            if($primary_key) {
                $sql .= '"' . $key . '" serial PRIMARY KEY ';
            } else {
                $sql .= '"' . $key . '" integer ';
            }
        } elseif($type == 'string') {
            $sql .= '"' . $key . '" VARCHAR(255) ';
        } elseif($type == 'text') {
            $sql .= '"' . $key . '" text ';
        } elseif($type == 'boolean') {
            $sql .= '"' . $key . '" boolean NOT NULL ';
            if(isset($extras['default'])) {
                $sql .= "DEFAULT '" . $extras['default'] . " '";
            } else {
                $sql .= "DEFAULT '" . 0 . " '";
            }
        } elseif($type == 'datetime') {
            $sql .= '"' . $key . '" timestamp ';
            if(isset($extras['default'])) {
                $sql .= "DEFAULT '" . $extras['default'] . " '";
            } else {
                $sql .= "DEFAULT NULL ";
            }
        } elseif($type == 'geometry') {
            $sql .= '"' . $key . '" geometry ';
        } elseif($type == 'integer') {
            $sql .= '"' . $key . '" integer NOT NULL ';
            if(isset($extras['default'])) {
                $sql .= "DEFAULT '" . $extras['default'] . " '";
            } else {
                $sql .= "DEFAULT '" . 0 . " '";
            }
        }

        return $sql;
    }

    public function dropTable($table) {
        $sql = '';

        // TODO: Be careful. Do not use with user passed in data. Need to prepare the table passed in.
        $sql = 'DROP TABLE "' . $table . '"';

        return $sql;
    }

    public static function insert($table, $input) {
        $sql = 'INSERT INTO ' . $table . '(';

        $args = [];
        foreach($input as $key => $value) {
            if(count($args) > 0) {
                $sql .= ',';
            }

            $sql .= '"' . $key . '"';
            $args[] = $value;
        }
        $sql .= ') VALUES (';

        $args = [];
        foreach($input as $key => $value) {
            if(count($args) > 0) {
                $sql .= ',';
            }

            $sql .= '?';
            $args[] = $value;
        }
        $sql .= ')';

        return $sql;
    }

    public static function update($table, $input) {
        // Make sure to include created_at and updated_at
        $sql = 'UPDATE ' . $table . ' SET ';
        $args = [];
        foreach($input as $key => $value) {
            if(count($args) > 0) {
                $sql .= ',';
            }
            $sql .= '"' . $key . '"' . ' = ?';
            $args[] = $value;
        }
        $sql .= ' WHERE id = ?';

        return $sql;
    }

}
