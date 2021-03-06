import SearchCriteria from './get-search-criteria.js';
import BackEnd from './get-backend.js';
import AddIcons from './add-icons.js';
import AddAddressesToMap from './add-addresses-to-map.js';

class MapMain {

  constructor() {
    this.set_find_properties_button();
  }

  set_find_properties_button() {
    let find_addresses = document.getElementById('find_addresses');
    if (typeof addresses_layer != 'undefined') {
      this.geoJSON.clearLayers();
    }
    find_addresses.addEventListener('click', () => {
      if (document.getElementById('towns').value == '' || document.getElementById('towns').value == 'not_selected') {
        document.getElementById('info').innerHTML = 'select a county and town, mogron...';
        return;
      } else {
        this.contact_backend(new SearchCriteria());
      }
    });
  }

  // contaxts backend 
  async contact_backend(criteria) {
    if (document.getElementById('info').value == '') return;
    let info = document.getElementById('info');
    info.innerHTML = 'contacting landregistry...';
    window.addresses = await new BackEnd(criteria);
    if (addresses.error) {
      return info.innerHTML = addresses.error + ': ' + addresses.error_no;
    }
    info.innerHTML = '';
    addresses = this.add_icons(addresses);
    this.add_addresses_to_map(addresses);
  }

  // adds icon url to each address
  add_icons(addresses) {
    addresses = new AddIcons(addresses);
    return addresses;
  }

  // adds new addresses to map and then fits the boundaries
  add_addresses_to_map(addresses) {
    this.new_addresses = new AddAddressesToMap();
    this.new_addresses.add_addresses(addresses);
    this.new_addresses.fit();
  }
}

export default MapMain;