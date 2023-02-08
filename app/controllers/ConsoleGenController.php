<?php

namespace app\controllers;

use DateTime;

class ConsoleGenController {
    public function cat($in, $out) {
        $category = [];
        $category_name = trim($out->prompt('Category Name (Title Case): '));

        $category_slug = trim($out->prompt('Slug (lowercase-dash): '));
        $category_user_ids = $out->prompt('Should this category be restricted to any user ids (Default: "", use a comma separated list): ');

        $category_premium_level = $out->prompt('Premium Level (Default: 0, Other Options: 10, 20, or 30): ', ['0', '10', '20', '30'], '0');

        $category = [
            'name' => $category_name,
            'slug' => $category_slug,
            'premium_level' => $category_premium_level,
            'user_ids' => $category_user_ids,
            'needs_connection' => 0,
        ];

        $numbers = [];
        $continue = 'y';
        do {
            $out->log('');
            $number_name = $out->prompt('Number Name (Title Case): ');
            $number_short_name = $out->prompt('Number Short Name (Default Number Title): ');
            $number_slug = $out->prompt('Number Slug (lowercase-dash): ');
            $number_needs_connection = $out->prompt('Number Needs Connection (Default: "0", Other Options: "1" for key based, or "2" for OAuth based): ', ['0', '1', '2'], '0');
            $number_user_ids = $out->prompt('Should this number be restricted to any user ids (Default: "", use a comma separated list): ');
            $number_premium_level = $out->prompt('Premium Level (Default: 0, Other Options: 10, 20, or 30): ', ['0', '10', '20', '30'], '0');

            if($number_needs_connection > $category['needs_connection']) {
                $category['needs_connection'] = $number_needs_connection;
            }


            $continue = $out->prompt('Add Another Number (y/n) (Default: n): ', ['y', 'n'], 'n');

            // Push to $numbers array
            $numbers[] = [
                'name' => $number_name,
                'short_name' => $number_short_name,
                'slug' => $number_slug,
                'needs_connection' => $number_needs_connection,
                'user_ids' => $number_user_ids,
                'premium_level' => $number_premium_level,
            ];

        } while($continue == 'y' || $continue == 'yes');
        $out->log('');

        /*
        $out->log('');
        $out->log('Category: ' . $category['name']);
        $out->log('Slug: ' . $category['slug']);
        $out->log('Needs Connection: ' . $category['needs_connection']);
        $out->log('Premium Level: ' . $category['premium_level']);

        foreach($numbers as $number) {
            $out->log('');
            $out->log('Number: ' . $number['name']);
            $out->log('Short Name: ' . $number['short_name']);
            $out->log('Slug: ' . $number['slug']);
            $out->log('Needs Connection: ' . $number['needs_connection']);
            $out->log('Premium Level: ' . $number['premium_level']);
        }
         */

        /* BOF: Temp Skip
        $category = [
            'name' => 'Stripe',
            'slug' => 'stripe',
            'premium_level' => 0,
            'needs_connection' => 1,
            'user_ids' => '',
        ];
        $numbers = [];
        $numbers[] = [
            'name' => 'MRR',
            'short_name' => 'MRR',
            'slug' => 'mrr',
            'needs_connection' => 1,
            'premium_level' => '0',
            'user_ids' => '',
        ];
        // EOF: Temp Skip
         */

        // Get the list of files to create.
        $files = $this->getFileList($category, $numbers);

        $exists = [];
        $new = [];
        foreach($files as $file) {
            if(is_file($file['path'])) {
                $exists[] = $file;
            } else {
                $new[] = $file;
            }
        }

        if(count($exists)) {
            $out->log('Files already exist as listed below. You will need to either delete these files or modify these files and manually add new files.', 'red');

            $out->log('');
            $out->log('Existing files:', 'red');
            foreach($exists as $file) {
                $out->log($file['path']);
            }

            $out->log('');
            $out->log('Files needing to be created:', 'green');
            foreach($new as $file) {
                $out->log($file['path']);
            }
            exit(1);
        } else {
            $out->log('Prepping to create the following files:', 'green');
            foreach($new as $file) {
                $out->log($file['path']);
            }
        }

        foreach($new as $file) {
            call_user_func($file['method'], $in, $out, $file, $category, $numbers);
        }

        $out->log('');
        $out->log('If there are any problems, you can delete the files running these commands (be very careful): ', 'red');
        foreach($new as $file) {
            $out->log('rm ' . $file['path']);
        }

        $out->log('');
        $out->log('NEXT STEPS: ', 'green');
        $out->log('• Run: php ao mig up');
        $out->log('• If using OAuth, make sure to add the service to app/controllers/OAuthController.php');
        $next_file_index = 2;
        if($category['needs_connection']) {
            $out->log('• Modify the connection view file as needed: ' . $files[3]['path']);
            $out->log('• May not need to make any changes but check the Connection Service file: ' . $files[2]['path']);
            $next_file_index = 4;
        }
        foreach($numbers as $number) {
            $out->log('• Modify the addTracking view file as needed: ' . $files[$next_file_index]['path']);
            $next_file_index++;
        }
        $out->log('• Modify the Main Service file as needed: ' . $files[0]['path']);
    }

    public function num($in, $out) {
        $category_slug = trim($out->prompt('Category Slug (lowercase-dash): '));

        $numbers = [];
        $continue = 'y';
        do {
            $out->log('');
            $number_name = $out->prompt('Number Name (Title Case): ');
            $number_short_name = $out->prompt('Number Short Name (Default Number Title): ');
            $number_slug = $out->prompt('Number Slug (lowercase-dash): ');
            $number_needs_connection = $out->prompt('Number Needs Connection (Default: "0", Other Options: "1" for key based, or "2" for OAuth based): ', ['0', '1', '2'], '0');
            $number_user_ids = $out->prompt('Should this number be restricted to any user ids (Default: "", use a comma separated list): ');
            $number_premium_level = $out->prompt('Premium Level (Default: 0, Other Options: 10, 20, or 30): ', ['0', '10', '20', '30'], '0');


            $continue = $out->prompt('Add Another Number (y/n) (Default: n): ', ['y', 'n'], 'n');

            // Push to $numbers array
            $numbers[] = [
                'name' => $number_name,
                'short_name' => $number_short_name,
                'slug' => $number_slug,
                'needs_connection' => $number_needs_connection,
                'user_ids' => $number_user_ids,
                'premium_level' => $number_premium_level,
            ];

        } while($continue == 'y' || $continue == 'yes');
        $out->log('');

        /*
        $out->log('');
        $out->log('Category Slug: ' . $category_slug);

        foreach($numbers as $number) {
            $out->log('');
            $out->log('Number: ' . $number['name']);
            $out->log('Short Name: ' . $number['short_name']);
            $out->log('Slug: ' . $number['slug']);
            $out->log('Needs Connection: ' . $number['needs_connection']);
            $out->log('Premium Level: ' . $number['premium_level']);
        }
         */

        /* BOF: Temp Skip
        $category = [
            'name' => 'Stripe',
            'slug' => 'stripe',
            'premium_level' => 0,
            'needs_connection' => 1,
            'user_ids' => '',
        ];
        $numbers = [];
        $numbers[] = [
            'name' => 'MRR',
            'short_name' => 'MRR',
            'slug' => 'mrr',
            'needs_connection' => 1,
            'premium_level' => '0',
            'user_ids' => '',
        ];
        // EOF: Temp Skip
         */

        // Get the list of files to create.
        $files = $this->getFileListAddition($category_slug, $numbers);

        $exists = [];
        $new = [];
        foreach($files as $file) {
            if(is_file($file['path'])) {
                $exists[] = $file;
            } else {
                $new[] = $file;
            }
        }

        if(count($exists)) {
            $out->log('Files already exist as listed below. You will need to either delete these files or modify these files and manually add new files.', 'red');

            $out->log('');
            $out->log('Existing files:', 'red');
            foreach($exists as $file) {
                $out->log($file['path']);
            }

            $out->log('');
            $out->log('Files needing to be created:', 'green');
            foreach($new as $file) {
                $out->log($file['path']);
            }
            exit(1);
        } else {
            $out->log('Prepping to create the following files:', 'green');
            foreach($new as $file) {
                $out->log($file['path']);
            }
        }

        foreach($new as $file) {
            call_user_func($file['method'], $in, $out, $file, $category_slug, $numbers);
        }

        $out->log('');
        $out->log('If there are any problems, you can delete the files running these commands (be very careful): ', 'red');
        foreach($new as $file) {
            $out->log('rm ' . $file['path']);
        }

        $out->log('');
        $out->log('NEXT STEPS: ', 'green');
        $out->log('• Run: php ao mig up');

        $next_file_index = 1;
        foreach($numbers as $number) {
            $out->log('• Modify the addTracking view file as needed: ' . $files[$next_file_index]['path']);
            $next_file_index++;
        }

        foreach($numbers as $number) {
            $out->log('• Modify the Service file as needed: ' . $files[$next_file_index]['path']);
            $next_file_index++;
        }
    }

    public function generateCategoryService($in, $out, $file, $category, $numbers) {
        $out->log('');
        $out->log('Creating Main Service file:', 'green');
        $out->log($file['path']);

        $tpl = ao()->file('app/settings/templates/CategoryService.php');
        $output = file_get_contents($tpl);

        $tpl2 = ao()->file('app/settings/templates/CategoryServiceMethods.php');
        $temp = file_get_contents($tpl2);
        $methods = '';
        foreach($numbers as $number) {
            $method = methodify($number['slug']);
            $methods .= str_replace('{{method}}', $method, $temp);
        }

        $class = classify($category['slug']);

        // Double brackets because it makes it easier to find the items in the template when editing the template.
        $output = str_replace('{{methods}}', $methods, $output);
        $output = str_replace('{{class}}', $class, $output);

        file_put_contents($file['path'], $output);
    }

    public function generateCategoryServiceAddition($in, $out, $file, $category_slug, $numbers) {
        $out->log('');
        $out->log('Creating Service file:', 'green');
        $out->log($file['path']);

        $tpl = ao()->file('app/settings/templates/CategoryServiceAddition.php');
        $output = file_get_contents($tpl);

        $tpl2 = ao()->file('app/settings/templates/CategoryServiceMethodsAddition.php');
        $temp = file_get_contents($tpl2);
        $methods = '';

        foreach($numbers as $number) {
            // If the number matches the file path.
            if($file['path'] == ao()->dir('app/services/trackings/' . $category_slug) . DIRECTORY_SEPARATOR . classify($category_slug) . classify($number['slug']) . 'Service.php') {
                $dir = ao()->dir('app/services/trackings/' . $category_slug);
                if(!is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }

                $method = methodify($number['slug']);
                $methods .= $temp;
                $methods = str_replace('{{method}}', $method, $methods);
                $methods = str_replace('{{class_slug}}', $category_slug, $methods);

                $class = classify($category_slug) . classify($number['slug']);

                // Double brackets because it makes it easier to find the items in the template when editing the template.
                $output = str_replace('{{methods}}', $methods, $output);
                $output = str_replace('{{class}}', $class, $output);
                $output = str_replace('{{class_slug}}', $category_slug, $output);

                file_put_contents($file['path'], $output);
                break;
            }
        }

    }


    public function generateConnectionServiceKey($in, $out, $file, $category, $numbers) {
        $out->log('');
        $out->log('Creating Key-Based Connection Service file:', 'green');
        $out->log($file['path']);

        $tpl = ao()->file('app/settings/templates/ConnectionServiceKey.php');
        $output = file_get_contents($tpl);

        $class = classify($category['slug']);
        $output = str_replace('{{class}}', $class, $output);

        file_put_contents($file['path'], $output);
    }

    public function generateConnectionServiceOAuth($in, $out, $file, $category, $numbers) {
        $out->log('');
        $out->log('Creating OAuth-Based Connection Service file:', 'green');
        $out->log($file['path']);

        $tpl = ao()->file('app/settings/templates/ConnectionServiceOAuth.php');
        $output = file_get_contents($tpl);

        $class = classify($category['slug']);
        $CLASS = upperfy($class);

        $output = str_replace('{{class}}', $class, $output);
        $output = str_replace('{{CLASS}}', $CLASS, $output);

        file_put_contents($file['path'], $output);
    }

    public function generateConnectionViewKey($in, $out, $file, $category, $numbers) {
        $out->log('');
        $out->log('Creating Key-Based Connection View file:', 'green');
        $out->log($file['path']);

        $tpl = ao()->file('app/settings/templates/connection_key.php');
        $output = file_get_contents($tpl);

        $cat_name = $category['name'];

        $output = str_replace('{{cat_name}}', $cat_name, $output);

        $dir = ao()->dir('app/views/numbers/' . $category['slug']);
        if(!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        file_put_contents($file['path'], $output);
    }

    public function generateConnectionViewOAuth($in, $out, $file, $category, $numbers) {
        $out->log('');
        $out->log('Creating OAuth-Based Connection View file:', 'green');
        $out->log($file['path']);

        $tpl = ao()->file('app/settings/templates/connection_oauth.php');
        $output = file_get_contents($tpl);

        $cat_name = $category['name'];
        $cat_slug = $category['slug'];

        $output = str_replace('{{cat_name}}', $cat_name, $output);
        $output = str_replace('{{cat_slug}}', $cat_slug, $output);

        $dir = ao()->dir('app/views/numbers/' . $category['slug']);
        if(!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        
        file_put_contents($file['path'], $output);
    }

    public function generateMigration($in, $out, $file, $category, $numbers) {
        $out->log('');
        $out->log('Creating Migration file:', 'green');
        $out->log($file['path']);

        $tpl = ao()->file('app/settings/templates/migration.php');
        $output = file_get_contents($tpl);

        $tpl2 = ao()->file('app/settings/templates/migration_numbers.php');
        $temp = file_get_contents($tpl2);
        $nums = '';
        foreach($numbers as $number) {
            $number_name = $number['name'];
            $number_short_name = $number['short_name'];
            $number_slug = $number['slug'];
            $number_user_ids = $number['user_ids'];
            $number_premium_level = $number['premium_level'];
            $number_needs_connection = $number['needs_connection'];

            $content = $temp;
            $content = str_replace('{{number_name}}', $number_name, $content);
            $content = str_replace('{{number_short_name}}', $number_short_name, $content);
            $content = str_replace('{{number_slug}}', $number_slug, $content);
            $content = str_replace('{{number_user_ids}}', $number_user_ids, $content);
            $content = str_replace('{{number_premium_level}}', $number_premium_level, $content);
            $content = str_replace('{{number_needs_connection}}', $number_needs_connection, $content);

            $nums .= $content;
        }

        $class_name = $category['name'];
        $class_slug = $category['slug'];
        $class_premium_level = $category['premium_level'];
        $class_user_ids = $category['user_ids'];

        // Double brackets because it makes it easier to find the items in the template when editing the template.
        $output = str_replace('{{numbers}}', $nums, $output);
        $output = str_replace('{{class_name}}', $class_name, $output);
        $output = str_replace('{{class_slug}}', $class_slug, $output);
        $output = str_replace('{{class_premium_level}}', $class_premium_level, $output);
        $output = str_replace('{{class_user_ids}}', $class_user_ids, $output);

        file_put_contents($file['path'], $output);
    }

    public function generateMigrationAddition($in, $out, $file, $category_slug, $numbers) {
        $out->log('');
        $out->log('Creating Migration file:', 'green');
        $out->log($file['path']);

        $tpl = ao()->file('app/settings/templates/migration_addition.php');
        $output = file_get_contents($tpl);

        $tpl2 = ao()->file('app/settings/templates/migration_numbers.php');
        $temp = file_get_contents($tpl2);
        $nums = '';
        foreach($numbers as $number) {
            $number_name = $number['name'];
            $number_short_name = $number['short_name'];
            $number_slug = $number['slug'];
            $number_user_ids = $number['user_ids'];
            $number_premium_level = $number['premium_level'];
            $number_needs_connection = $number['needs_connection'];

            $content = $temp;
            $content = str_replace('{{number_name}}', $number_name, $content);
            $content = str_replace('{{number_short_name}}', $number_short_name, $content);
            $content = str_replace('{{number_slug}}', $number_slug, $content);
            $content = str_replace('{{number_user_ids}}', $number_user_ids, $content);
            $content = str_replace('{{number_premium_level}}', $number_premium_level, $content);
            $content = str_replace('{{number_needs_connection}}', $number_needs_connection, $content);

            $nums .= $content;
        }

        $class_slug = $category_slug;

        // Double brackets because it makes it easier to find the items in the template when editing the template.
        $output = str_replace('{{numbers}}', $nums, $output);
        $output = str_replace('{{class_slug}}', $class_slug, $output);

        file_put_contents($file['path'], $output);
    }

    public function generateNumberView($in, $out, $file, $category, $numbers) {
        $out->log('');
        $out->log('Creating Number View file:', 'green');
        $out->log($file['path']);

        $tpl = ao()->file('app/settings/templates/number.php');
        $output = file_get_contents($tpl);

        $cat_name = $category['name'];

        $output = str_replace('{{cat_name}}', $cat_name, $output);

        $dir = ao()->dir('app/views/numbers/' . $category['slug']);
        if(!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        file_put_contents($file['path'], $output);
    }

    public function generateNumberViewAddition($in, $out, $file, $category_slug, $numbers) {
        $out->log('');
        $out->log('Creating Number View file:', 'green');
        $out->log($file['path']);

        $tpl = ao()->file('app/settings/templates/number.php');
        $output = file_get_contents($tpl);

        $dir = ao()->dir('app/views/numbers/' . $category_slug);
        if(!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        file_put_contents($file['path'], $output);
    }

    public function getFileList($category, $numbers) {
        $list = [];

        // Main Service file
        $file = [];
        $file['method'] = [$this, 'generateCategoryService'];
        $file['path'] = ao()->dir('app/services/trackings') . DIRECTORY_SEPARATOR . classify($category['slug'])  . 'Service.php';
        $list[] = $file;

        // Migration file
        $today = new DateTime();
        $dt = $today->format('Y_m_d_H_i_s_');
        $file = [];
        $file['method'] = [$this, 'generateMigration'];
        $file['path'] = ao()->dir('db/migrations') . DIRECTORY_SEPARATOR . $dt . $category['slug'] . '.php';
        $list[] = $file;

        if($category['needs_connection'] > 0) {
            // Connection Service
            $file = [];
            if($category['needs_connection'] == 2) {
                $file['method'] = [$this, 'generateConnectionServiceOAuth'];
            } else {
                $file['method'] = [$this, 'generateConnectionServiceKey'];
            }
            $file['path'] = ao()->dir('app/services/connections') . DIRECTORY_SEPARATOR . classify($category['slug'])  . 'ConnectionService.php';
            $list[] = $file;

            // Connection View
            $file = [];
            if($category['needs_connection'] == 2) {
                $file['method'] = [$this, 'generateConnectionViewOAuth'];
            } else {
                $file['method'] = [$this, 'generateConnectionViewKey'];
            }
            $file['path'] = ao()->dir('app/views/numbers/' . $category['slug']) . DIRECTORY_SEPARATOR . 'connection.php';
            $list[] = $file;
        }

        foreach($numbers as $number) {
            // Number View
            $file = [];
            $file['method'] = [$this, 'generateNumberView'];
            $file['path'] = ao()->dir('app/views/numbers/' . $category['slug']) . DIRECTORY_SEPARATOR . dashify($number['slug'])  . '.php';
            $list[] = $file;
        }

        return $list;
    }

    public function getFileListAddition($category_slug, $numbers) {
        $list = [];

        // Migration file
        $today = new DateTime();
        $dt = $today->format('Y_m_d_H_i_s_');
        $file = [];
        $file['method'] = [$this, 'generateMigrationAddition'];
        $file['path'] = ao()->dir('db/migrations') . DIRECTORY_SEPARATOR . $dt . $category_slug . '.php';
        $list[] = $file;

        foreach($numbers as $number) {
            // Number View
            $file = [];
            $file['method'] = [$this, 'generateNumberViewAddition'];
            $file['path'] = ao()->dir('app/views/numbers/' . $category_slug) . DIRECTORY_SEPARATOR . dashify($number['slug'])  . '.php';
            $list[] = $file;
        }

        foreach($numbers as $number) {
            // Service file
            $file = [];
            $file['method'] = [$this, 'generateCategoryServiceAddition'];
            $file['path'] = ao()->dir('app/services/trackings/' . $category_slug) . DIRECTORY_SEPARATOR . classify($category_slug) . classify($number['slug']) . 'Service.php';
            $list[] = $file;
        }

        return $list;
    }

}
