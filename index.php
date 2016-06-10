<?php


/*
Plugin Name: Gravity Forms - Typeform Addon
Plugin URI:  http://typeform.com
Description: Grativy Forms addon that renders typeforms in the fly
Version:     0.1
Author:      Jepser | Typeform
Author URI:  http://typeform.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

$includes = [
    // 'settings.php',
    'tf-data-structure.php',
    'tf-api.php',
    'tf-gf.php',
    // 'capture.php'
];

foreach ($includes as $i) {
    include $i;
}
