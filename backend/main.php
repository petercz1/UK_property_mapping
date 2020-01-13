<?php

declare(strict_types=1);
namespace chipbug\postcodes;
session_start();

// dumps debug stuff into a debug.log file in this directory
// error_log('stuff'); for simple debug,
// error_log(print_r($my_array, true)); for arrays.
ini_set("log_errors", "1");
ini_set("error_log", getcwd() . "/debug.log");

require_once(dirname(__FILE__). "/libraries/sparqllib.php");
require_once("getpropertiessold.php");
require_once("getcoords.php");
require_once("makegeojson.php");

$request = json_decode(file_get_contents('php://input'), true);

$properties_sold = (new GetPropertiesSold)->init($request);
$properties_with_coords = (new GetCoords)->init($properties_sold);
$geojson = (new MakeGeoJson)->init($properties_with_coords);

echo json_encode($geojson);