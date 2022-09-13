<?php

$content = <<<PHP
<?php

// This is a key file generation page. Keys are used for encrypting data in the database. If you are setting up 
// NumbersQ, please follow the instructions below.
//
// Instructions For Set Up: 
//   1. Save this file as .keys.php to the main directory of the project.
//      * Meaning, this file should be saved in the same top directory where the LICENSE file is located.
//   2. If you are not sure how to save a file, try one of the options below:
//      * Try pressing `<CTRL> + S` or `<Command> + S` (the "+" means press at the same time).
//      * Try right clicking the page and look for something like "Save as..." in the context menu that is 
//        displayed.
//      * Try opening your browser's main menu and look for an option like "Save page as..." (it may be located in
//        a submenu).
//   3. Note that the saved file starts with a "." - files that start with a period are call "hidden files" and 
//      some systems will hide those files in a normal file browser. If you are not seeing your saved file on 
//      your system, you may need to turn on a setting in your directory viewer called something like 
//      "Show hidden files" to actually see the file.
//   4. Once you have NumbersQ set up on your system, do not change these values, otherwise all your data will be 
//      lost. Changing the key values is outside the scope of these instructions, but the basic steps are to add 
//      new values below (not replace, add - something like: CONNECTIONS_2). Once the new values are in place, you 
//      then want to update the data in your database to use the new values.

// Do not change these values, otherwise all of the encrypted data in the database will become unusable.

return [

PHP;


foreach($keys as $key) {
	$content .= "    '" . $key['name'] . "' => '" . $key['value'] . "',\n";
}


$content .= '];';

echo $content;
