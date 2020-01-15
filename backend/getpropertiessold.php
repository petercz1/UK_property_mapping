<?php

declare(strict_types=1);
namespace chipbug\postcodes;

// dumps debug stuff into a debug.log file in this directory
// error_log('stuff'); for simple debug,
// error_log(print_r($my_array, true)); for arrays.
ini_set("log_errors", "1");
ini_set("error_log", getcwd() . "/debug.log");

/**
 * GetPropertiesSold
 *
 */
class GetPropertiesSold
{
    private $town;
    private $min;
    private $max;
    private $date_sold;
    private $postcode;

    public function init($search_criteria)
    {
        // TODO - sanitize incoming data if deploying to the public!!!
        $this->town =$search_criteria['town'];
        $this->min =$search_criteria['min'];
        $this->max =$search_criteria['max'];
        $this->date_sold =$search_criteria['date_sold'];
        $this->postcode =$search_criteria['postcode'];

        return $this->get_properties();
    }

    /**
     * Queries landregistry.data.gov.uk
     * constructs a sparql text query to retrieve properties sold
     *
     * @param string $town
     * @param string $postcode_start
     * @param string $date
     * @param string $min_amount
     *
     * @return array
     */
    private function get_properties():array
    {
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
     
        $sparql = "SELECT ?paon ?saon ?street ?town ?county ?postcode ?amount ?date ?property_type
    WHERE
    {
        FILTER( ?town = \"$this->town\"^^xsd:string )
        FILTER( strstarts( str(?postcode), \"$this->postcode\"^^xsd:string) )
        FILTER( ?date > \"$this->date_sold\"^^xsd:date )
        FILTER( ?amount > $this->min && ?amount < $this->max)
    
        ?addr   lrcommon:postcode ?postcode ;
                lrcommon:town ?town .

        ?transx ppd:propertyAddress ?addr ;
                ppd:pricePaid ?amount ;
                ppd:transactionDate ?date ;
                ppd:transactionCategory <http://landregistry.data.gov.uk/def/ppi/standardPricePaidTransaction> ;
                ppd:propertyType ?property_type .
    
        OPTIONAL {?addr lrcommon:county ?county}
        OPTIONAL {?addr lrcommon:paon ?paon}
        OPTIONAL {?addr lrcommon:saon ?saon}
        OPTIONAL {?addr lrcommon:street ?street}
    }
    ";
        $result = sparql_query($sparql);
        if (!$result) {
            return ['error'=>'landregistry error', 'error_no'=> sparql_errno() . ': ' . sparql_error()];
        }
        $result= (array)$result;
        return $result['rows'];
    }
}