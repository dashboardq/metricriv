<?php

use mavoc\core\Clean;

if(!function_exists('ao')) {
    function ao() {
        global $ao;
        return $ao;
    }
}

if(!function_exists('bufferHide')) {
    function bufferHide() {   
        ob_end_clean();
    }   
}

if(!function_exists('bufferStart')) {
    function bufferStart() {   
        ob_start();
    }   
}

if(!function_exists('calc')) {
    // Need to eventually update this to use $args = func_get_args();
    // This is going to be really ugly and messy and only work for certain cases for now.
    // Really need to create a parser:
    // https://github.com/dmaevsky/rd-parse
    // https://en.wikipedia.org/wiki/Parse_tree
    function calculate($formula, $x = 0, $y = 0, $z = 0) {   
        $formula = preg_replace('/\s+/', '', $formula);
        if(strtolower($formula) == 'x') {
            $output = $x;
        } elseif(strtolower($formula) == 'y') {
            $output = $y;
        } elseif(strtolower($formula) == 'z') {
            $output = $z;
        } else {
            $output = $formula;
        }

        preg_match_all('/[+\/*\^%-(]|ceil/', $output, $matches);
        $operators = $matches[0];
        while(count($operators)) {
            if(in_array('ceil', $operators)) {
                preg_match('/(.*)ceil\((.*)\)(.*)/', $output, $args);

                $a = calculate(trim($args[2]), $x, $y, $z);
                $output = $args[1] . operate('ceil', $a) . $args[3];
            } elseif(in_array('(', $operators)) {
                preg_match('/(.*)\((.*)\)(.*)/', $output, $args);

                $a = calculate(trim($args[2]), $x, $y, $z);
                $output = $args[1] . operate('parenthesis', $a) . $args[3];
            } elseif(in_array('^', $operators)) {
                preg_match('/(.*)([xyz0-9.]+)\^([xyz0-9.]+)(.*)/', $output, $args);

                $a = calculate(trim($args[2]), $x, $y, $z);
                $b = calculate(trim($args[3]), $x, $y, $z);
                $output = $args[1] . operate('^', $a, $b) . $args[4];
            } elseif(in_array('%', $operators)) {
                preg_match('/(.*)([xyz0-9.]+)%([xyz0-9.]+)(.*)/', $output, $args);

                $a = calculate(trim($args[2]), $x, $y, $z);
                $b = calculate(trim($args[3]), $x, $y, $z);
                $output = $args[1] . operate('%', $a, $b) . $args[4];
            } elseif(in_array('/', $operators)) {
                preg_match('/(.*)([xyz0-9.]+)\/([xyz0-9.]+)(.*)/', $output, $args);

                $a = calculate(trim($args[2]), $x, $y, $z);
                $b = calculate(trim($args[3]), $x, $y, $z);
                $output = $args[1] . operate('/', $a, $b) . $args[4];
            } elseif(in_array('*', $operators)) {
                preg_match('/(.*)([xyz0-9.]+)\*([xyz0-9.]+)(.*)/', $output, $args);

                $a = calculate(trim($args[2]), $x, $y, $z);
                $b = calculate(trim($args[3]), $x, $y, $z);
                $output = $args[1] . operate('*', $a, $b) . $args[4];
            } elseif(in_array('-', $operators)) {
                preg_match('/(.*)([xyz0-9.]+)-([xyz0-9.]+)(.*)/', $output, $args);

                $a = calculate(trim($args[2]), $x, $y, $z);
                $b = calculate(trim($args[3]), $x, $y, $z);
                $output = $args[1] . operate('-', $a, $b) . $args[4];
            } elseif(in_array('+', $operators)) {
                preg_match('/(.*)([xyz0-9.]+)\+([xyz0-9.]+)(.*)/', $output, $args);

                $a = calculate(trim($args[2]), $x, $y, $z);
                $b = calculate(trim($args[3]), $x, $y, $z);
                $output = $args[1] . operate('+', $a, $b) . $args[4];
            }

            preg_match_all('/[+\/*\^%-(]|ceil/', $output, $matches);
            $operators = $matches[0];
        }

        return $output;
    }   
}

if(!function_exists('classify')) {
    function classify($input) {
        $input = ao()->hook('helper_split_input', $input);
        // Add a space before uppercase letters (make sure the first letter is not uppercase).
        $words = preg_replace('/(?=[A-Z])/', ' $0', lcfirst($input));
        $words = preg_replace('/[\s,-_]+/', ' ', strtolower($words));
        $words = ucwords($words);
        $words = ao()->hook('ao_helpers_classify_words', $words);
        $output = str_replace(' ', '', $words);
        return $output;
    }
}

if(!function_exists('clean')) {
    function clean($input, $cleaner, $default = null) {
        if($default) {
            $clean = new Clean(['field' => $input], ['field' => [[$cleaner => $default]]]);
        } else {
            $clean = new Clean(['field' => $input], ['field' => [$cleaner]]);
        }

        return $clean->fields['field'];
    }
}

if(!function_exists('dangerous')) {
    function dangerous($input) {
        echo $input;
    }
}

if(!function_exists('dashify')) {
    function dashify($input) {
        $input = ao()->hook('helper_split_input', $input);
        // Add a space before uppercase letters (make sure the first letter is not uppercase).
        $words = preg_replace('/(?=[A-Z])/', ' $0', lcfirst($input));
        $words = preg_replace('/[\s,-_]+/', ' ', strtolower($words));
        $parts = explode(' ', $words);
        if(count($parts)) {
            $parts[0] = strtolower($parts[0]);
        }
        $output = implode('-', $parts);
        return $output;
    }
}

if(!function_exists('data')) {
    function data($input) {   
        $output = [];
        if(is_array($input)) {
            foreach($input as $i => $item) {
                $output[$i] = simplify($item->data);
            }
        } else {
            $output = simplify($input->data);
        }
        return $output;
    }   
} 

if(!function_exists('dc')) {
    // Dump and continue
    function dc($input) {
        echo '<pre>'; 
        print_r($input); 
        echo '</pre>'; 
    }
}

if(!function_exists('dd')) {
    // Dump and die
    function dd($input) {
        echo '<pre>'; 
        print_r($input); 
        echo '</pre>'; 
        die;
    }
}

if(!function_exists('dj')) {
    // Dump json and die
    function dj($input) {
        if(is_array($input) || $input instanceof \stdClass) {
            echo json_encode($input);
            die;
        } else {
            $arr = ( array ) $input;
            $output = [];
            // Need to add more types and loop through the children (without hitting infinite recursion like print_r)
            // Maybe consider print_r to json code:
            // https://stackoverflow.com/questions/43522927/convert-print-r-array-of-objects-string-to-json-in-php
            foreach($arr as $key => $value ) {
                if(
                    is_string($value)
                    || is_numeric($value)
                ) {
                    $output[$key] = $value;
                }
            }
            echo json_encode($output);
            die;

            // Need to look at adding a way to turn a class instance (like Request and Response) to JSON.
            // Consider using "implements JsonSerializable": 
            // https://www.php.net/manual/en/jsonserializable.jsonserialize.php
            // Or something like the example here (the AbstractEntity class):
            // https://stackoverflow.com/questions/9858448/converting-object-to-json-and-json-to-object-in-php-library-like-gson-for-java
            echo 'The object cannot be converted to JSON.';
            die;
        }
    }
}


if(!function_exists('debugSql')) {
    // PDO sends the query and parameters separately to the database.
    // Sometimes I want to copy and paste the final query directly into the DB but there is no way
    // to see the final query using PDO. This method simulates the final query. This should only be
    // used in a debug setting where you are making test database calls.
    //
    // This assumes you are using question marks for the parameters.
    //
    // The queries output by this function are not safe or properly escaped. This is just to help with debugging.
    //
    // TODO: Need to add quotes around any params that are strings.
    function debugLastSql($args) {
        $sql = '';
        if(isset($args[0])) {
            $sql = $args[0];
            $params = array_slice($args, 1);
            if(isset($params[0]) && is_array($params[0])) {
                $params = $params[0];
            }
            $position = strpos($sql, '?');
            $i = 0;
            while($position !== false) {
                $value = $params[$i];
                if(is_string($value)) {
                    $sql = substr_replace($sql, "'" . $value . "'", $position, strlen('?'));
                } else {
                    $sql = substr_replace($sql, $value, $position, strlen('?'));
                }
                $position = strpos($sql, '?');
                $i++;
            }
        }

        // Remove any newlines
        $sql = preg_replace('/\s*\r\n\s*|\s*\n\s*|\s*\r\s*/', ' ', $sql);

        // Remove whitespace;
        $sql = trim($sql);

        // Add a final semicolon for easy copying
        $sql .= ';';

        return $sql;
    }
}

// From: https://stackoverflow.com/questions/1416697/converting-timestamp-to-time-ago-in-php-e-g-1-day-ago-2-days-ago
// https://stackoverflow.com/a/18602474
// Slightly modified to accept DateTime objects.
if(!function_exists('elapsed')) {
    /*
    function elapsed($datetime, $full = false) {
        $now = new \DateTime;
        if(is_a($datetime, 'DateTime')) {
            $ago = $datetime;
        } else {
            $ago = new \DateTime($datetime);
        }
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        //$string = array(
            //'y' => 'year',
            //'m' => 'month',
            //'w' => 'week',
            //'d' => 'day',
            //'h' => 'hour',
            //'i' => 'minute',
            //'s' => 'second',
        //);
        //foreach ($string as $k => &$v) {
            //if ($diff->$k) {
                //$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            //} else {
                //unset($string[$k]);
            //}
        //}
        $string = array(
            'y' => 'y',
            'm' => 'm',
            'w' => 'w',
            'd' => 'd',
            'h' => 'h',
            'i' => 'm',
            's' => 's',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . '' . $v;
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
     */
    function elapsed($datetime, $full = false) {
        $now = new \DateTime;
        if(is_a($datetime, 'DateTime')) {
            $ago = $datetime;
        } else {
            $ago = new \DateTime($datetime);
        }
        $diff = $now->diff($ago);

        $output = '';
        if($diff->y) {
            $output = $diff->y . 'y ago';
        } elseif($diff->m) {
            $output = $diff->m . 'mo ago';
        } elseif($diff->d) {
            if($diff->d >= 7) {
                $output = floor($diff->d/7) . 'w ago';
            } else {
                $output = $diff->d . 'd ago';
            }
        } elseif($diff->h) {
            $output = $diff->h . 'h ago';
        } elseif($diff->i) {
            $output = $diff->i . 'm ago';
        } elseif($diff->s) {
            $output = $diff->s . 's ago';
        } else {
            $output = 'just now';
        }

        return $output;
    }
}

if(!function_exists('_esc')) {
    function _esc($value, $double_encode = true) {   
        //return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8', $double_encode);
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8', $double_encode);
    }   
} 

if(!function_exists('esc')) {
    function esc($value, $double_encode = true) {   
        echo _esc($value, $double_encode);
    }   
} 

// From: https://stackoverflow.com/questions/1416697/converting-timestamp-to-time-ago-in-php-e-g-1-day-ago-2-days-ago
// https://stackoverflow.com/a/18602474
// Slightly modified to accept DateTime objects.
if(!function_exists('future')) {
    /*
    function future($datetime, $full = false) {
        $now = new \DateTime;
        if(is_a($datetime, 'DateTime')) {
            $ago = $datetime;
        } else {
            $ago = new \DateTime($datetime);
        }
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        //$string = array(
            //'y' => 'year',
            //'m' => 'month',
            //'w' => 'week',
            //'d' => 'day',
            //'h' => 'hour',
            //'i' => 'minute',
            //'s' => 'second',
        //);
        //foreach ($string as $k => &$v) {
            //if ($diff->$k) {
                //$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            //} else {
                //unset($string[$k]);
            //}
        //}
        $string = array(
            'y' => 'y',
            'm' => 'm',
            'w' => 'w',
            'd' => 'd',
            'h' => 'h',
            'i' => 'm',
            's' => 's',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . '' . $v;
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
     */
    function future($datetime, $full = false) {
        $now = new \DateTime;
        if(is_a($datetime, 'DateTime')) {
            $ago = $datetime;
        } else {
            $ago = new \DateTime($datetime);
        }
        $diff = $now->diff($ago);

        $output = '';
        if($diff->y) {
            $output = $diff->y . 'y to go';
        } elseif($diff->m) {
            $output = $diff->m . 'mo to go';
        } elseif($diff->d) {
            if($diff->d >= 7) {
                $output = floor($diff->d/7) . 'w to go';
            } else {
                $output = $diff->d . 'd to go';
            }
        } elseif($diff->h) {
            $output = $diff->h . 'h to go';
        } elseif($diff->i) {
            $output = $diff->i . 'm to go';
        } elseif($diff->s) {
            $output = $diff->s . 's to go';
        } else {
            $output = 'just now';
        }

        return $output;
    }
}



if(!function_exists('linkify')) {
    // From https://daringfireball.net/2010/07/improved_regex_for_matching_urls
    // (Public Domain mentioned at the end of the article)
    // https://gist.github.com/gruber/8891611
    // Modified for PHP (comment example on the gist)
    // Modified to make sure links start with "http://"
    function linkify($input) {
        // Add http:// to the non https? links
        //$output = preg_replace("/(?i)\b((?:(?<!@)[a-z0-9]+(?:[.\-][a-z0-9]+)*[.](?:com|net|org|edu|gov|mil|aero|asia|biz|cat|coop|info|int|jobs|mobi|museum|name|post|pro|tel|travel|xxx|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|Ja|sk|sl|sm|sn|so|sr|ss|st|su|sv|sx|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)\b\/?(?!@)))/", "<a href='http://$1'>$1</a>", $input);
        $input = preg_replace("/(?i)\b((?:(?<!https:\/\/|http:\/\/|@|\.|>)[a-z0-9]+(?:[.\-][a-z0-9]+)*[.](?:com|net|org|edu|gov|mil|aero|asia|biz|cat|coop|info|int|jobs|mobi|museum|name|post|pro|tel|travel|xxx|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|Ja|sk|sl|sm|sn|so|sr|ss|st|su|sv|sx|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)\b\/?(?!@)))/", '<a href="http://$1">$1</a>', $input);

        $output = preg_replace("/(?i)\b((?<!\")(?:https?:(?:\/{1,3}|[a-z0-9%])|[a-z0-9.\-]+[.](?:com|net|org|edu|gov|mil|aero|asia|biz|cat|coop|info|int|jobs|mobi|museum|name|post|pro|tel|travel|xxx|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|Ja|sk|sl|sm|sn|so|sr|ss|st|su|sv|sx|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)\/)(?:[^\s()<>{}\[\]]+|\([^\s()]*?\([^\s()]+\)[^\s()]*?\)|\([^\s]+?\))+(?:\([^\s()]*?\([^\s()]+\)[^\s()]*?\)|\([^\s]+?\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/", "<a href='$1'>$1</a>", $input);

        // The full regex is split into two parts above to be able to add "http://" to the beginning of 
        // non-http domains.
        //$output = preg_replace("/(?i)\b((?:https?:(?:\/{1,3}|[a-z0-9%])|[a-z0-9.\-]+[.](?:com|net|org|edu|gov|mil|aero|asia|biz|cat|coop|info|int|jobs|mobi|museum|name|post|pro|tel|travel|xxx|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|Ja|sk|sl|sm|sn|so|sr|ss|st|su|sv|sx|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)\/)(?:[^\s()<>{}\[\]]+|\([^\s()]*?\([^\s()]+\)[^\s()]*?\)|\([^\s]+?\))+(?:\([^\s()]*?\([^\s()]+\)[^\s()]*?\)|\([^\s]+?\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’])|(?:(?<!@)[a-z0-9]+(?:[.\-][a-z0-9]+)*[.](?:com|net|org|edu|gov|mil|aero|asia|biz|cat|coop|info|int|jobs|mobi|museum|name|post|pro|tel|travel|xxx|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|Ja|sk|sl|sm|sn|so|sr|ss|st|su|sv|sx|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)\b\/?(?!@)))/", "<a href='$1'>$1</a>", $input);

        return $output;
    }
}

if(!function_exists('meta')) {
    function meta($pagination) {   
        $output = [];
        $output = $pagination;
        return $output;
    }   
} 

if(!function_exists('methodify')) {
    function methodify($input) {
        $input = ao()->hook('helper_split_input', $input);
        // Add a space before uppercase letters (make sure the first letter is not uppercase).
        $words = preg_replace('/(?=[A-Z])/', ' $0', lcfirst($input));
        $words = preg_replace('/[\s,-_]+/', ' ', strtolower($words));
        $words = ucwords($words);
        $parts = explode(' ', $words);
        if(count($parts)) {
            $parts[0] = strtolower($parts[0]);
        }
        $output = implode('', $parts);
        return $output;
    }
}

if(!function_exists('now')) {
    function now($return_datetime = false) {
        $dt = new \DateTime();
        if($return_datetime) {
            return $dt;
        } else {
            return $dt->format('Y-m-d H:i:s');
        }
    }
}

if(!function_exists('num')) {
    // Works like number_format() but allows you to pass in strings that already have a comma.
    function num($num, $decimals = 0, $decimal_separator = ".", $thousands_separator = ",") {
        // Make sure the comma is removed before running through number_format
        $output = str_replace(',', '', $num);
        $output = number_format($output, $decimals, $decimal_separator, $thousands_separator);
        return $output;
    }
}

if(!function_exists('operate')) {
    // Probably eventually need to build a parser.
    function operate($operator, $x = 0, $y = 0, $z = 0) {   
        if($operator == '+') {
            $output = $x + $y;
        } elseif($operator == '-') {
            $output = $x - $y;
        } elseif($operator == '*') {
            $output = $x * $y;
        } elseif($operator == '/') {
            $output = $x / $y;
        } elseif($operator == '^') {
            $output = pow($x, $y);
        } elseif($operator == '%') {
            $output = $x % $y;
        } elseif($operator == 'ceil') {
            $output = ceil($x);
        } elseif($operator == 'parenthesis') {
            // Nothing actually calculated since parenthesis by themselves just determine priority.
            $output = $x;
        }
        return $output;
    }   
}
if(!function_exists('_out')) {
    function _out($input, $color = null) {   
        $output = $input;

        // I prefer "if" over "switch".
        if($color == 'green') {
            $output = "\033[32m" . $output;
        } elseif($color == 'red') {
            $output = "\033[31m" . $output;
        }

        if($color) {
            $output .= "\033[0m";
        }

        $output .= "\n";

        return $output;
    }   
} 

if(!function_exists('out')) {
    function out($input, $color = null) {   
        echo _out($input, $color);
    }   
} 

if(!function_exists('pluralize')) {
    function pluralize($count = 0, $singular) {   
        // Very hacky approach. Eventually need to switch to something like this:
        // http://kuwamoto.org/2007/12/17/improved-pluralizing-in-php-actionscript-and-ror/
        if($count != 1) {
            return $count . ' ' . $singular . 's';
        } else {
            return $count . ' ' . $singular;
        }
    }   
} 

if(!function_exists('returnFalse')) {
    function returnFalse() {   
        return false;
    }   
}

if(!function_exists('returnTrue')) {
    function returnTrue() {   
        return true;
    }   
}

if(!function_exists('rmdirForce')) {
    // Based on https://stackoverflow.com/questions/1653771/how-do-i-remove-a-directory-that-is-not-empty
    function rmdirForce($dir) {   
        if(!file_exists($dir)) {
            return true;
        }

        if(!is_dir($dir)) {
            return unlink($dir);
        }

        foreach(scandir($dir) as $item) {
            if($item == '.' || $item == '..') {
                continue;
            }

            if(!rmdirForce($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }   
}

if(!function_exists('simplify')) {
    function simplify($input) {
        $output = [];
        foreach($input as $key => $item) {
            if(is_array($item)) {
                $output[$key] = simplify($item);
            } elseif($item instanceof DateTime) {
                $output[$key] = $item->format('c');
            } else {
                $output[$key] = $item;
            }
        }

        return $output;
    }
}

if(!function_exists('success')) {
    function success($message = []) {   
        $output = [];
        $output['status'] = 'success';
        if(is_array($message)) {
            $output['messages'] = $message;
        } else {
            $output['messages'] = [$message];
        }
        $output['meta'] = new \stdClass();
        $output['data'] = new \stdClass();
        return $output;
    }   
} 

if(!function_exists('underscorify')) {
    function underscorify($input) {
        $input = ao()->hook('helper_split_input', $input);
        // Add a space before uppercase letters (make sure the first letter is not uppercase).
        $words = preg_replace('/(?=[A-Z])/', ' $0', lcfirst($input));
        $words = preg_replace('/[\s,-_]+/', ' ', strtolower($words));
        $parts = explode(' ', $words);
        if(count($parts)) {
            $parts[0] = strtolower($parts[0]);
        }
        $output = implode('_', $parts);
        return $output;
    }
}

if(!function_exists('upperify')) {
    function upperify($input) {
        $input = ao()->hook('helper_split_input', $input);
        // Add a space before uppercase letters (make sure the first letter is not uppercase).
        $words = preg_replace('/(?=[A-Z])/', ' $0', lcfirst($input));
        $output = preg_replace('/[\s,-_]+/', '_', strtoupper($words));
        return $output;
    }   
}

// If outputing to HTML, you probably want to use url() which will also escape the content.
if(!function_exists('_uri')) {
    function _uri($input) {
        $output = '';
        $output .= ao()->env('APP_SITE');
        $output .= '/';
        $output .= trim($input, '/');
        return $output;
    }
}
if(!function_exists('uri')) {
    function uri($input) {
        echo _uri($input);
    }
}

if(!function_exists('_url')) {
    function _url($input) {
        $output = '';
        $output .= ao()->env('APP_SITE');
        $output .= '/';
        $output .= trim($input, '/');

        $output = _esc($output);
        return $output;
    }
}
if(!function_exists('url')) {
    function url($input) {
        echo _url($input);
    }
}

if(!function_exists('wordify')) {
    function wordify($input) {
        $input = ao()->hook('helper_split_input', $input);
        // Add a space before uppercase letters (make sure the first letter is not uppercase).
        $words = preg_replace('/(?=[A-Z])/', ' $0', lcfirst($input));
        $words = preg_replace('/[\s,-_]+/', ' ', strtolower($words));

        // Uppercase any abbreviations
        // Acronyms won't have any vowels (some may but this is just a rough working example for now) 
        $parts = explode(' ', $words);
        foreach($parts as $i => $part) {
            // If the word does not have a vowel or "y", it is probably an acronym.
            if(!preg_match('/[AEIOUYaeiouy]+/', $part)) {
                $parts[$i] = strtoupper($part);
            } elseif(in_array(strtolower($part), ['id'])) {
                // Make Id fully uppercase (probably a better way to do this, but this works for now)
                $parts[$i] = strtoupper($part);
            }
        }
        $words = implode(' ', $parts);

        $words = ucwords($words);
        $output = $words;
        $output = ao()->hook('helper_wordify_output', $words);
        return $output;
    }
}
