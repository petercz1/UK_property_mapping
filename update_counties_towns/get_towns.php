<?php

declare(strict_types=1);
namespace chipbug\postcodes;

/**
 * Get towns and 2-letter postcode for each county
 *
 */
class GetTowns
{
    public function init($county)
    {
        $raw_towns_data = $this->get_towns($county);
        return $this->cleanup($raw_towns_data);
    }

    /**
     * Queries landregistry.data.gov.uk
     * constructs a sparql text query to retrieve towns in chosen county
     *
     * @param string $town
     * @param string $postcode_start
     * @param string $date
     * @param string $min_amount
     *
     * @return array
     */

    private function get_towns($county):array
    {
        $db = sparql_connect("https://landregistry.data.gov.uk/landregistry/query");
        if (!$db) {
            print sparql_errno() . ": " . sparql_error(). "\n";
            exit;
        }
        sparql_ns("lrcommon", "http://landregistry.data.gov.uk/def/common/");
        sparql_ns("xsd", "http://www.w3.org/2001/XMLSchema#");

     
        $sparql = "SELECT DISTINCT ?town ?shortcode
    WHERE
    {
        VALUES ?county {\"$county\"^^xsd:string}

        ?addr lrcommon:county \"$county\" .
        ?addr lrcommon:town ?town .
        ?addr lrcommon:postcode ?postcode . 

        BIND(strbefore(?postcode, ' ') as ?shortcode)  
    }
    ORDER BY ?town
    ";
    
        $result = sparql_query($sparql);
        if (!$result) {
            print sparql_errno() . ": " . sparql_error(). "\n";
            exit;
        }
    
        $fields = sparql_field_array($result);
        $result= (array)$result;
        return $result['rows'];
    }

    private function cleanup($towns_array):array
    {
        $cleaned_array = [];
        foreach ($towns_array as $town) {
            $cleaned_town['town'] = ucwords(strtolower($town['town']['value']));
            $cleaned_town['shortcode'] = $town['shortcode']['value'];
            $cleaned_array[] = $cleaned_town;
        }
        return $cleaned_array;
    }
}
