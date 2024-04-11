<?php

namespace mavoc\core\db;

use PDO;

class MySQL {
    public static function alterTableAdd($table, $args) {
        $sql = '';

        $field_count = 0;

        $sql = '';
        // TODO: Be careful. Do not use with user passed in data. Need to prepare the table passed in.
        $sql .= 'ALTER TABLE `' . $table . '` ';
        foreach($args as $key => $arg) {
            if(is_string($arg)) {
                if($field_count) {
                    $sql .= ', ';
                }
                $sql .= 'ADD COLUMN ' . self::createType($key, $arg);

                $field_count++;
            } elseif(is_array($arg) && isset($arg['type'])) {
                if($field_count) {
                    $sql .= ', ';
                }
                $sql .= 'ADD COLUMN ' . self::createType($key, $arg['type'], $arg);
                if(isset($arg['after'])) {
                    $sql .= ' AFTER `' . $arg['after'] . '`';
                }

                $field_count++;
            }
        }

        return $sql;
    }

    public static function alterTableDrop($table, $args) {
        $sql = '';

        $field_count = 0;

        $sql = 'ALTER TABLE `' . $table . '` ';
        // TODO: Be careful. Do not use with user passed in data. Need to prepare the table passed in.
        foreach($args as $i => $column) {
            if($field_count) {
                $sql .= ', ';
            }
            $sql .= 'DROP COLUMN `' . $column . '`';

            $field_count++;
        }

        return $sql;
    }

    public static function alterTableModify($table, $args) {
        $sql = '';

        $field_count = 0;

        $sql = '';
        // TODO: Be careful. Do not use with user passed in data. Need to prepare the table passed in.
        $sql .= 'ALTER TABLE `' . $table . '` ';
        foreach($args as $key => $arg) {
            if(is_string($arg)) {
                if($field_count) {
                    $sql .= ', ';
                }
                $sql .= 'MODIFY COLUMN ' . self::createType($key, $arg);

                $field_count++;
            } elseif(is_array($arg) && isset($arg['type'])) {
                if($field_count) {
                    $sql .= ', ';
                }
                $sql .= 'MODIFY COLUMN ' . self::createType($key, $arg['type'], $arg);
                if(isset($arg['after'])) {
                    $sql .= ' AFTER `' . $arg['after'] . '`';
                }

                $field_count++;
            }
        }

        return $sql;
    }

    public static function alterTableRename($table, $args) {
        $sql = '';

        // TODO: Be careful. Do not use with user passed in data. Need to prepare the table passed in.
        foreach($args as $old => $new) {
            $sql .= 'ALTER TABLE `' . $table . '` RENAME COLUMN `' . $old . '` TO `' . $new . '`; ';
        }

        return $sql;
    }

    public static function createTable($table, $args) {
        $sql = '';

        $field_count = 0;
        $primary_key = '';

        $sql = '';
        // TODO: Be careful. Do not use with user passed in data. Need to prepare the table passed in.
        $sql .= 'CREATE TABLE `' . $table . '` ( ';
        foreach($args as $key => $arg) {
            if(is_string($arg)) {
                if($field_count) {
                    $sql .= ', ';
                }

                // Save the first $primary_key
                if($arg == 'id' && !$primary_key) {
                    $sql .= self::createType($key, $arg, ['primary' => true]);
                    $primary_key = $key;
                } else {
                    $sql .= self::createType($key, $arg);
                }

                $field_count++;
            } elseif(is_array($arg) && isset($arg['type'])) {
                if($field_count) {
                    $sql .= ', ';
                }

                // Save the first $primary_key
                if($arg == 'id' && !$primary_key) {
                    if(!isset($arg['primary'])) {
                        $arg['primary'] = true;
                    }
                    $sql .= self::createType($key, $arg['type'], $arg);
                    $primary_key = $key;
                } else {
                    $sql .= self::createType($key, $arg['type'], $arg);
                }

                $field_count++;
            }
        }

        if($primary_key) {
            $sql .= ', PRIMARY KEY (`' . $primary_key . '`)';
        }
        $sql .= ' )';

        return $sql;
    }

    public static function createType($key, $type = '', $extras = []) {
        $sql = '';

        if($type == 'id') {
            $sql .= '`' . $key . '` bigint unsigned NOT NULL ';
            if(isset($extras['primary'])) {
                $sql .= ' AUTO_INCREMENT ';
            } else {
                if(isset($extras['default'])) {
                    $sql .= "DEFAULT '" . $extras['default'] . "' ";
                } else {
                    $sql .= "DEFAULT '" . 0 . "' ";
                }
            }
        } elseif($type == 'string') {
            $sql .= '`' . $key . '` varchar(255) ';
        } elseif($type == 'text') {
            $sql .= '`' . $key . '` longtext ';
        } elseif($type == 'boolean') {
            $sql .= '`' . $key . '` tinyint(1) NOT NULL ';
            if(isset($extras['default'])) {
                $sql .= "DEFAULT '" . $extras['default'] . "' ";
            } else {
                $sql .= "DEFAULT '" . 0 . "' ";
            }
        } elseif($type == 'datetime') {
            $sql .= '`' . $key . '` datetime ';
            if(isset($extras['default'])) {
                $sql .= "DEFAULT '" . $extras['default'] . "' ";
            } else {
                $sql .= "DEFAULT NULL ";
            }
        } elseif($type == 'geometry') {
            $sql .= '`' . $key . '` geometry ';
        } elseif($type == 'integer') {
            $sql .= '`' . $key . '` int NOT NULL ';
            if(isset($extras['default'])) {
                $sql .= "DEFAULT '" . $extras['default'] . "' ";
            } else {
                $sql .= "DEFAULT '" . 0 . "' ";
            }
        }

        return $sql;
    }

    public function dropTable($table) {
        $sql = '';

        // TODO: Be careful. Do not use with user passed in data. Need to prepare the table passed in.
        $sql = 'DROP TABLE `' . $table . '`';

        return $sql;
    }

    public static function insert($table, $input) {
        $sql = 'INSERT INTO ' . $table . ' SET ';
        $args = [];
        foreach($input as $key => $value) {
            if(count($args) > 0) {
                $sql .= ',';
            }
            $sql .= '`' . $key . '`' . ' = ?';
            $args[] = $value;
        }

        return $sql;
    }

    public static function truncateTable($table) {
        $sql = '';

        // TODO: Be careful. Do not use with user passed in data. Need to prepare the table passed in.
        $sql = 'TRUNCATE `' . $table . '`';

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
            $sql .= '`' . $key . '`' . ' = ?';
            $args[] = $value;
        }
        $sql .= ' WHERE id = ?';

        return $sql;
    }

}
