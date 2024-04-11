<?php

namespace mavoc\core;

use mavoc\core\db\MySQL;
use mavoc\core\db\PostgreSQL;

use PDO;

use DateTime;
use DateTimeZone;

class DB {
    public $dsn;
    public $options;
    public $pdo;
    public $type = '';

    public $last_insert_id = 0;
    public $last_insert_table = '';

    public $quote = '"';

    public function __construct() {
    }   

    public function init() {
        // Based on / Inspired by: https://phpdelusions.net/pdo
        // If you are new to databases, you should read this: https://phpdelusions.net/sql_injection
        $this->type = ao()->env('DB_TYPE');
        $host = ao()->env('DB_HOST');
        $db = ao()->env('DB_NAME');
        $user = ao()->env('DB_USER');
        $pass = ao()->env('DB_PASS');
        $charset = ao()->env('DB_CHARSET');

        // database source name
        $dsn_suffix = ":host=$host;dbname=$db;user=$user;password=$pass";
        if($this->type == 'mysql') {
            $this->dsn = 'mysql' . $dsn_suffix;
            $this->quote = '`';
        } elseif($this->type == 'pgsql') {
            $this->dsn = 'pgsql' . $dsn_suffix;
            $this->quote = '"';
        }
        $this->options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            // It sounds like as of PHP 7.4, MySQL can parse user and password from the dsn.
            //$this->pdo = new PDO($this->dsn, $user, $pass, $this->options);
            $this->pdo = new PDO($this->dsn, null, null, $this->options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }

        ao()->hook('ao_db_loaded');
    }

    public function array($input) {
        $output = [];
        foreach($input as $row) {
            foreach($row as $key => $value) {
                $output[] = $value;
            }
        }

        return $output;
    }

    public function call($args, $type = PDO::FETCH_ASSOC) {
        $args = ao()->hook('ao_db_call_args', $args);
        $type = ao()->hook('ao_db_call_type', $type);
        $args_count = count($args);
        $output = [];

        if($args_count > 0) {
            $prepared = $this->pdo->prepare($args[0]);
            if($args_count >= 2 && is_array($args[1])) {
                $result = $prepared->execute($args[1]);
            } else {
                $result = $prepared->execute(array_slice($args, 1));
            }

            if($result === false) {
                return false;
            }

            $output = $prepared->fetchAll($type);

            $output = ao()->hook('ao_db_call_output', $output);
            return $output;
        } else {
            $output = ao()->hook('ao_db_call_output', false);
            return $output;
        }   
    } 

    public function alterTableAdd($table, $args) {
        $sql = '';

        if(in_array($this->type, ['mysql'])) {
            $sql = MySQL::alterTableAdd($table, $args);
        } elseif(in_array($this->type, ['pgsql'])) {
            $sql = PostgreSQL::alterTableAdd($table, $args);
        }

        return $sql;
    }

    public function alterTableDrop($table, $args) {
        $sql = '';

        if(in_array($this->type, ['mysql'])) {
            $sql = MySQL::alterTableDrop($table, $args);
        } elseif(in_array($this->type, ['pgsql'])) {
            $sql = PostgreSQL::alterTableDrop($table, $args);
        }

        return $sql;
    }

    public function alterTableModify($table, $args) {
        $sql = '';

        if(in_array($this->type, ['mysql'])) {
            $sql = MySQL::alterTableModify($table, $args);
        } elseif(in_array($this->type, ['pgsql'])) {
            $sql = PostgreSQL::alterTableModify($table, $args);
        }

        return $sql;
    }

    public function alterTableRename($table, $args) {
        $sql = '';

        if(in_array($this->type, ['mysql'])) {
            $sql = MySQL::alterTableRename($table, $args);
        } elseif(in_array($this->type, ['pgsql'])) {
            $sql = PostgreSQL::alterTableRename($table, $args);
        }

        return $sql;
    }

    public function createTable($table, $args) {
        $sql = '';

        if(in_array($this->type, ['mysql'])) {
            $sql = MySQL::createTable($table, $args);
        } elseif(in_array($this->type, ['pgsql'])) {
            $sql = PostgreSQL::createTable($table, $args);
        }

        return $sql;
    }

    public function dropTable($table) {
        $sql = '';

        if(in_array($this->type, ['mysql'])) {
            $sql = MySQL::dropTable($table);
        } elseif(in_array($this->type, ['pgsql'])) {
            $sql = PostgreSQL::dropTable($table);
        }

        return $sql;
    }

    // get('field_name', $sql, $values)
    public function get() {
        $args = func_get_args();
        $field = $args[0];
        $results = DB::call(array_slice($args, 1));

        if(count($results)) {
            $output = $results[0][$field];
        } else {
            $output = '';
        }

        return $output;
    }

    public function lastInsertId() {
        if(in_array($this->type, ['mysql'])) {
            $output = $this->pdo->lastInsertId();
        } elseif(in_array($this->type, ['pgsql'])) {
            // May consider switching this to calling:
            // SELECT currval('TABLE_ID_seq')
            // The sequence name may not actually be needed - I added it because I was having problems
            // when initially starting out.
            // This won't work: https://code.djangoproject.com/ticket/9302
            //$output = $this->pdo->lastInsertId($this->last_insert_table . '_' . 'id' . '_' . 'seq');
            $output = $this->pdo->lastInsertId();
        }
        return $output;
    }

    // Gets a list of a single column's values
    // list('field_name', $sql, $values)
    public function list() {
        $args = func_get_args();
        $field = $args[0];
        $results = DB::call(array_slice($args, 1));

        if(count($results)) {
            foreach($results as $item) {
                $output[] = $item[$field];
            }
        } else {
            $output = [];
        }

        return $output;
    }

    public function query() {
        $args = func_get_args();
        $args = ao()->hook('ao_db_query_args', $args);
        $output = DB::call($args);
        $output = ao()->hook('ao_db_query_output', $output);
        return $output;
    }

    public function insert($table, $input) {
        $items = [];
        $items['created_at'] = new DateTime();
        $items['updated_at'] = new DateTime();

        $input = array_merge($items, $input);

        // Make sure to include created_at and updated_at
        if(in_array($this->type, ['mysql'])) {
            $sql = MySQL::insert($table, $input);
        } elseif(in_array($this->type, ['pgsql'])) {
            $sql = PostgreSQL::insert($table, $input);
        }

        foreach($input as $key => $value) {
            // Prep data (like converting DateTime to string)
            if($value instanceof DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            }

            $args[] = $value;
        }

        //$this->db->query($sql, $args);
        ao()->db->query($sql, $args);

        $this->last_insert_table = $table;
    }

    public function truncateTable($table) {
        $sql = '';

        if(in_array($this->type, ['mysql'])) {
            $sql = MySQL::truncateTable($table);
        } elseif(in_array($this->type, ['pgsql'])) {
            $sql = PostgreSQL::truncateTable($table);
        }

        return $sql;
    }

    public function update($table, $id, $input = []) {
        if(in_array($this->type, ['mysql'])) {
            $sql = MySQL::update($table, $input);
        } elseif(in_array($this->type, ['pgsql'])) {
            $sql = PostgreSQL::update($table, $input);
        }

        foreach($input as $key => $value) {
            // Prep data (like converting DateTime to string
            if($value instanceof DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            }

            $args[] = $value;
        }
        $args[] = $id;

        //$this->db->query($sql, $args);
        ao()->db->query($sql, $args);
    }
}
