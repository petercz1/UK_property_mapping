<?php

declare(strict_types=1);
namespace chipbug\postcodes;

// dumps debug stuff into a debug.log file in this directory
// error_log('stuff'); for simple debug,
// error_log(print_r($my_array, true)); for arrays.
ini_set("log_errors", "1");
ini_set("error_log", getcwd() . "/debug.log");

class GetCoords
{
    public function init($properties):array
    {
        $postcodes = $this->prepare_postcodes($properties);
        
        $coords_total = [];
        foreach ($postcodes as $postcode_subarray=>$value) {
            //$value = ['postcodes'=>$value];
            $result = $this->get_coords($value);
            $coords_total = array_merge($coords_total, $result['result']);
        }

        $properties = $this->add_coords_to_properties($properties, (array)$coords_total);
        return $properties;
    }

    /**
     * sends array of postcode addresses to postcodes.io
     *
     * @param [type] $postcodes
     * @return array
     */
    private function get_coords($postcodes):array
    {
        $postcodes = json_encode($postcodes);

        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL, "Https://api.postcodes.io/postcodes?filter=postcode,longitude,latitude");
        \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postcodes);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($postcodes))
        );
        $coords = \curl_exec($ch);
        return json_decode($coords, true);
    }

    /**
     * Generates an array of postcodes for postcode.io - max 100 per query
     * It chunks $properties up into sub-arrays of max 100 and then
     * creates an array per 100 ready for turning into a json object like so:
     * {
     *     "postcodes" : ["PR3 0SG", "M45 6GN", "EX165BL"]
     * }
     *
     * @param [type] $properties
     * @return void
     */
    private function prepare_postcodes($properties)
    {
        // chunk postocdes into lots of max 100 for postcodes.io query
        $properties = array_chunk($properties, 100);

        $all_postcodes = array();
        foreach ($properties as $property=>$value) {
            $postcodes_subset = array();
            foreach($value as $address_item=>$address_detail){
                if ($address_detail['postcode']) {
                    array_push($postcodes_subset, $address_detail['postcode']['value']);
                }
            }
            array_push($all_postcodes, ['postcodes'=>$postcodes_subset]);
        }
        return $all_postcodes;
    }

    private function add_coords_to_properties($properties, $coords)
    {
        foreach ($properties as $property=>&$property_value) {
            foreach ($coords as $coord=>$coords_value) {
                if ($property_value['postcode']['value'] == $coords_value['result']['postcode']) {
                    $coord_details = array('coordinates'=>[$coords_value['result']['longitude'], $coords_value['result']['latitude']]);
                    $property_value = $property_value + $coord_details;
                }
            }
            if($property_value['coordinates'] == null){
                unset($properties[$property]);
            }
        }
        return $properties;
    }
}
