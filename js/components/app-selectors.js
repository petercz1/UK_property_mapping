import RootElement from '../libraries/rootelement.js';
import counties_and_towns from '../data/counties_and_towns.js';
// import SearchCritera from '../map/get-search-criteria.js';

class appSelectors extends RootElement {

  constructor() {
    super();
    this.renderData();
    this.set_counties();
    this.format_prices();
  }

  renderData() {
    return this.innerHTML = `
	<div id="selectors">
		<select name="counties" id="counties"></select>
		<select name="towns" id="towns"></select>

		<span>
		<input type="text" placeholder="postcode" id="shortcode" disabled>
		</span>

		<span>
		<input type="text" id="postcode" placeholder="eg 1DR">
		</span>

		<input type="text" id="min" class="prices" placeholder="min" value="£100,000">
		<span class="type">to</span>
		<input type="text" id="max" class="prices" placeholder="max" value="£300,000">

		<span class="type">
		<label for="date_sold">Sold since</label>
		<input type="date" id="date_sold" value="2018-01-01">
		</span>

		<button type="button" id="find_addresses">find</button>
	</div>
    `;
  }

  set_counties() {
    let counties_dd = document.getElementById('counties');
    let option = document.createElement('option');
    option.value = 'not_selected';
    option.innerHTML = 'select a county...';
    counties_dd.append(option);
    // counties are loaded from counties.js
    let counties_list = Object.keys(counties_and_towns);
    counties_list.forEach(function (county) {
      let option = document.createElement('option');
      option.value = county;
      option.innerHTML = county;
      counties_dd.append(option);
    });
    counties_dd.addEventListener('change', () => {
      this.set_towns(document.getElementById('counties').value)
    });
    return;
  }

  set_towns(selected_county) {
    // grab towns dropdown
    let towns_dd = document.getElementById('towns');

    towns_dd.disabled = true;
    let option = document.createElement('option');
    option.value = 'not_selected';
    option.innerHTML = 'getting towns...';
    towns_dd.append(option);

    let towns = counties_and_towns[selected_county];
    towns_dd.innerHTML = '';
    towns_dd.disabled = false;
    option.value = 'not_selected';
    option.innerHTML = 'select a town...';
    towns_dd.append(option);
    towns.forEach(function (town) {
      let option = document.createElement('option');
      option.value = '{"town":"' + town.town + '","shortcode":"' + town.shortcode + '"}'; //create json-like string to easily pass 2 data items
      option.innerHTML = town.town + ' (' + town.shortcode + ')';
      towns_dd.append(option);
    });
    towns_dd.addEventListener('change', () => {
      this.set_shortcode(JSON.parse(document.getElementById('towns').value))
    });
    return;
  }

  set_shortcode(town_obj) {
    let shortcode_input = document.getElementById('shortcode');
    shortcode_input.value = town_obj.shortcode;
  }

  // unused function but might come in handy if I want to bracket dates?
  set_max_date() {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    if (dd < 10) {
      dd = '0' + dd
    }
    if (mm < 10) {
      mm = '0' + mm
    }

    today = yyyy + '-' + mm + '-' + dd;
    document.getElementById("date_sold").setAttribute("max", today);
  }

  format_prices() {
    let prices = document.getElementsByClassName('prices');
    Array.from(prices).forEach(function (price) {
      price.addEventListener('change', e => {
        let formatted = price.value.replace(/[£,]+/g, "");
        formatted = parseInt(formatted);
        formatted = formatted.toLocaleString("en", {
          style: "currency",
          currency: "GBP",
          minimumFractionDigits: 0,
          maximumFractionDigits: 0,
        });
        price.value = formatted;
      })
    });
  }
}

customElements.define('app-selectors', appSelectors);

export default appSelectors;