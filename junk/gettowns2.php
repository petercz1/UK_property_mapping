<?php

declare(strict_types=1);
namespace chipbug\postcodes;

require_once(dirname(__FILE__). "/libraries/sparqllib.php");

// dumps debug stuff into a debug.log file in this directory
ini_set("log_errors", "1");
ini_set("error_log", getcwd() . "/debug.log");

$request = file_get_contents('php://input');
$request =\strtoupper($request);

$result = json_encode((new GetTowns)->init($request));
echo $result;

/**
 * GetPropertiesSold
 *
 */
class GetTowns
{
    public function init($county)
    {
        error_log('inside init');
        error_log($county);
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
        error_log('inside get_towns');
        $db = sparql_connect("https://landregistry.data.gov.uk/landregistry/query");
        if (!$db) {
            print sparql_errno() . ": " . sparql_error(). "\n";
            exit;
        }
        sparql_ns("rdf", "http://www.w3.org/1999/02/22-rdf-syntax-ns#");
        sparql_ns("rdfs", "http://www.w3.org/2000/01/rdf-schema#");
        sparql_ns("owl", "http://www.w3.org/2002/07/owl#");
        sparql_ns("xsd", "http://www.w3.org/2001/XMLSchema#");
        sparql_ns("sr", "http://data.ordnancesurvey.co.uk/ontology/spatialrelations/");
        sparql_ns("ukhpi", "http://landregistry.data.gov.uk/def/ukhpi/");
        sparql_ns("lrppi", "http://landregistry.data.gov.uk/def/ppi/");
        sparql_ns("skos", "http://www.w3.org/2004/02/skos/core#");
        sparql_ns("lrcommon", "http://landregistry.data.gov.uk/def/common/");
        sparql_ns("ppd", "http://landregistry.data.gov.uk/def/ppi/");
     
        $sparql = "SELECT DISTINCT ?town
    WHERE
    {
        VALUES ?county {\"$county\"^^xsd:string}

        ?addr lrcommon:county \"$county\" .
        ?addr lrcommon:town ?town .
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
        error_log('result:');
        error_log(print_r($result['rows'], true));
        return $result['rows'];
    }

    private function cleanup($towns_array):array
    {
        error_log('inside cleanup');
        $cleaned_array = [];
        foreach ($towns_array as $town) {
            $cleaned_array[] = ucwords(strtolower($town['town']['value']));
        }

        return $cleaned_array;
    }
}
