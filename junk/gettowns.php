<?php

$counties_and_towns = unserialize(file_get_contents('counties_and_towns.txt'));

error_log(print_r($counties_and_towns, true));

