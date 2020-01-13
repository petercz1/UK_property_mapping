<?php

declare(strict_types=1);
namespace chipbug\postcodes;

// dumps debug stuff into a debug.log file in this directory
// error_log('stuff'); for simple debug,
// error_log(print_r($my_array, true)); for arrays.
ini_set("log_errors", "1");
ini_set("error_log", getcwd() . "/debug.log");

class MakeGeoJson
{
    private $geoJSON = [];

    public function init($properties):array
    {
        $properties = $this->make_geojson($properties);
        return $properties;
    }

    /**
     * converts properties array into a geojson-ready php array
     * Gets converted by main.js by 'echo json_encode($properties);'
     *
     * @param [type] $postcodes
     * @return array
     */
    private function make_geojson($properties):array
    {
        $this->geoJSON['type'] = "FeatureCollection";
        $this->geoJSON['features'] = [];

        // Each GeoJSON feature needs 3 keys - type, geometry, properties
        foreach ($properties as $property=>$property_value) {

            // set feature type = Feature
            $geofeature['type'] = 'Feature'; // set 'type'

            // set feature geometry = point with coordinates
            $geometry['type'] = 'Point';
            $geometry['coordinates'] = $property_value['coordinates'];
            $geofeature['geometry'] = $geometry; // set 'geometry'
            unset($property_value['coordinates']);
            unset($geometry);
            
            // set feature properties - work through them and delete to leave just the address
            // price sold for first...
            $geofeatureprops['amount'] = $property_value['amount']['value'];
            unset($property_value['amount']);
            
            // ... then date sold...
            $date = \DateTime::createFromFormat('Y-m-d', $property_value['date']['value']);
            $geofeatureprops['date'] = $date->format('d/m/Y');
            unset($property_value['date']);
            unset($date);
                        
            // ... then building type, stripping out unneeded http url stuff
            $geofeatureprops['property_type'] = str_replace('http://landregistry.data.gov.uk/def/common/', '', $property_value['property_type']['value']);
            unset($property_value['property_type']);
            
            // ... finally concatenate the remaining fields as the address, comma separated, capitalized apart from postcode
            $street_address = [];
            foreach ($property_value as $prop=>$value) {
                if ($prop != 'postcode') {
                    $street_address[] = $this->uc($value['value']);
                } else {
                    $street_address[] = $value['value'];
                }
            }
            $street_address = implode(', ', array_filter($street_address));
            $geofeatureprops['street_address'] = $street_address;

            $search_address = [];
            foreach ($property_value as $prop=>$value) {
                if ($prop == 'street') {
                    $search_address[] = "\"" . $value['value'] . "\"";
                } else {
                    $search_address[] = $value['value'];
                }
            }
            $search_address = implode(', ', array_filter($search_address));
            $geofeatureprops['search_address'] = $search_address;

            // set up google search for images link
            $geofeatureprops['url'] = "http://www.google.com/search?tbm=isch&q=" . urlencode($geofeatureprops['property_type']) . '+for+sale+' . urlencode($search_address) . "+property+for+sale";
            unset($street_address);
            unset($search_address);

            // Now set all properties in geofeature...
            $geofeature['properties'] = $geofeatureprops; // set 'properties' for feature
            unset($geofeatureprops);
            // ... and finally append to 'features' array in geoJSON
            $this->geoJSON['features'][] = $geofeature; // add feature to features array
            unset($geofeature);
        }
        return $this->geoJSON;
    }

    /**
     * uc - converts upper-case text to first letter upper-case and the rest lower case
     *
     * @param [type] $txt
     * @return string
     */
    private function uc($txt):string
    {
        $txt = strtolower($txt);
        $txt = ucwords($txt);
        return $txt;
    }
}