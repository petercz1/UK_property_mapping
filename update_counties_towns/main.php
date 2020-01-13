<?php

declare(strict_types=1);
namespace chipbug\postcodes;

// dumps debug stuff into a debug.log file in this directory
ini_set("log_errors", "1");
ini_set("error_log", getcwd() . "/debug.log");

require_once(dirname(__FILE__). "/../backend/libraries/sparqllib.php");
require_once("counties.php"); // counties as array
require_once("get_towns.php");

$all_town_and_counties = [];
$counter = 0;

foreach ($counties as $county) {
    error_log('doing ' . $county);
    $county = strtoupper($county);
    $towns = (new GetTowns)->init($county);
    $county = ucwords(strtolower($county));
    $all_town_and_counties[$county] = $towns;
}

// build a js file as ES6 doesn't allow importing a pure json file. Grrr...
file_put_contents(dirname(__FILE__). "/../js/data/counties_and_towns.js", "let counties_and_towns = ");
file_put_contents(dirname(__FILE__). "/../js/data/counties_and_towns.js", json_encode($all_town_and_counties) . ";\n\n", FILE_APPEND);
file_put_contents(dirname(__FILE__). "/../js/data/counties_and_towns.js", "export default counties_and_towns;", FILE_APPEND);
