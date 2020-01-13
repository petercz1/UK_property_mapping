class SearchCriteria {

  constructor() {
	  return this.get_search_criteria();
  }

  get_search_criteria() {
    let search_criteria = {};

    // grab town name and convert to uppercase
    let town_obj = JSON.parse(document.getElementById('towns').value);
    search_criteria.town = town_obj.town.toUpperCase();

    // strip currency stuff from min and max amounts
    let min = document.getElementById('min').value.replace(/[£,]+/g, "");
    search_criteria.min = parseInt(min);
    let max = document.getElementById('max').value.replace(/[£,]+/g, "");
    search_criteria.max = parseInt(max);

    // get date sold
    search_criteria.date_sold = document.getElementById('date_sold').value;

    // combine shortcode (eg PO33) with postcode (eg 1OH) and convert to uppercase
    search_criteria.postcode = document.getElementById('shortcode').value.toUpperCase() + ' ' + document.getElementById('postcode').value.toUpperCase();

    let tmp = {
      town: "RYDE",
      min: "100000",
      max: "300000",
      date_sold: "2019-01-01",
      postcode: "PO33"
    }
    return (search_criteria);
    //return (tmp);
  }

}

export default SearchCriteria;