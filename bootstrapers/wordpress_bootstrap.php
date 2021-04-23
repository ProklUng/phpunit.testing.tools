<?php

set_time_limit(999999999999999999);

$_SERVER['REQUEST_METHOD'] = 'GET';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

global $gost, $iso;
$gost = $iso = [];

if (!isset($wp_did_header)) {
    $wp_did_header = true;

    // Load the WordPress library.
    require_once(__DIR__.'/../../wp-load.php');


    // Set up the WordPress query. WP_QUERY args! https://wp-kama.ru/function/wp_query
    //  ['category_name' => 'video']
    wp();

    // Load the theme template.
    require_once(__DIR__.'/../../wp-includes/template-loader.php');
}


WP_Mock::bootstrap();
