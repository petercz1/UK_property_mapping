import RootElement from '../libraries/rootelement.js';
import AddAddressesToMap from '../map/add-addresses-to-map.js';

class appCheckboxes extends RootElement {

  constructor() {
    super();
    // this.renderData();
    // this.check_selector();
  }

  renderData() {
    window.legend = L.control({
      position: 'topright'
    });
    legend.onAdd = (map) => {
      let div = L.DomUtil.create('div');
      div.innerHTML = `
      <div id="checkboxes">
          <span class="type detached">
            <input type="checkbox" value="detached" id="detached" checked>
            <label for="detached" id="detached_label">detached</label>
          </span>
    
          <span class="type flat">
            <input type="checkbox" value="flat" id="flat" checked>
            <label for="flat" id="flat_label">flat</label>
          </span>
    
          <span class="type terraced">
            <input type="checkbox" value="terraced" id="terraced" checked>
            <label for="terraced" id ="terraced_label">terraced</label>
          </span>
    
          <span class="type semi">
            <input type="checkbox" value="semi" id="semi" checked>
            <label for="semi" id="semi_label">semi</label>
        </span>
        </div>
        <div id="info"></div>
        `;
      return div;
    };
    legend.addTo(map);
    //   this.innerHTML = `
    // <div id="checkboxes">

    //     <span class="type detached">
    //       <input type="checkbox" value="detached" id="detached" checked>
    //       <label for="detached" id="detached_label">detached</label>
    //     </span>

    //     <span class="type flat">
    //       <input type="checkbox" value="flat" id="flat" checked>
    //       <label for="flat" id="flat_label">flat</label>
    //     </span>

    //     <span class="type terraced">
    //       <input type="checkbox" value="terraced" id="terraced" checked>
    //       <label for="terraced" id ="terraced_label">terraced</label>
    //     </span>

    //     <span class="type semi">
    //       <input type="checkbox" value="semi" id="semi" checked>
    //       <label for="semi" id="semi_label">semi</label>
    //   </span>
    //   <span id="info"></span>
    //   </div>
    // 	`;
  }

  check_selector() {
    let check_selector = document.getElementById('checkboxes');
    check_selector.addEventListener('change', function () {
      if (addresses) {
        map.removeLayer(addresses_layer);
        this.new_addresses = new AddAddressesToMap();
        this.new_addresses.add_addresses(addresses);
      }
    })
  }
}

customElements.define('app-checkboxes', appCheckboxes);

export default appCheckboxes;