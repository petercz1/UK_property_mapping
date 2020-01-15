class SearchCriteria {

  constructor() {
	  return this.get_search_criteria();
  }

  get_search_criteria() {
    let search_criteria = {};

    // grab town name and convert to uppercase
    console.log(document.getElementById('towns').value);
    if(document.getElementById('towns').value == '' || document.getElementById('towns').value == 'not selected'){
      return 
    }
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

    return (search_criteria);
  }
}

export default SearchCriteria;