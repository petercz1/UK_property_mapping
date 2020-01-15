class AddAddressesToMap {

  async add_addresses(addresses) {

    // set counters for checkboxes
    let detached = document.getElementById('detached_label');
    let semi = document.getElementById('semi_label');
    let terraced = document.getElementById('terraced_label');
    let flat = document.getElementById('flat_label');
    let count_detached = await addresses.features.filter(feature => feature.properties.property_type == 'detached').length;
    let count_terraced = await addresses.features.filter(feature => feature.properties.property_type == 'terraced').length;
    let count_semi = await addresses.features.filter(feature => feature.properties.property_type == 'semi-detached').length;
    let count_flat = await addresses.features.filter(feature => feature.properties.property_type == 'flat-maisonette').length;
    detached.textContent = 'detached (' + count_detached + ')';
    semi.textContent = 'semi (' + count_semi + ')';
    terraced.textContent = 'terraced (' + count_terraced + ')';
    flat.textContent = 'flat (' + count_flat + ')';

    window.pointToLayer = (feature, latlng) => {
      window.price_icon = new L.Icon({
        iconUrl: feature.properties.iconUrl
      })
      return L.marker(latlng, {
        icon: price_icon
      })
    };

    window.oms = new OverlappingMarkerSpiderfier(map);
    oms.addListener('click', (marker) => {
      this.addr = document.createElement('a');
      this.text_node = document.createTextNode(marker.feature.properties.street_address);
      this.addr.appendChild(this.text_node);
      this.addr.href = marker.feature.properties.url;
      this.addr.target = '_blank';
      this.popup = new L.Popup();
      this.popup.setContent(this.addr);
      this.popup.setLatLng(marker.getLatLng());
      map.openPopup(this.popup);
    })

    window.options = {
      pointToLayer: pointToLayer,
      onEachFeature: (feature, latlng) => {
        oms.addMarker(latlng);
      },
      filter: function (feature, layer) {
        let detached = document.getElementById('detached');
        if (detached.checked) {
          if (feature.properties.property_type == "detached") return true
        }
        let semi = document.getElementById('semi');
        if (semi.checked) {
          if (feature.properties.property_type == "semi-detached") return true
        }
        let terraced = document.getElementById('terraced');
        if (terraced.checked) {
          if (feature.properties.property_type == "terraced") return true
        }
        let flat = document.getElementById('flat');
        if (flat.checked) {
          if (feature.properties.property_type == "flat-maisonette") return true
        }
      }
    }

    if (typeof (addresses_layer != 'undefined')) {
      map.removeLayer(addresses_layer);
    }
    window.addresses_layer = new L.geoJSON(addresses, options);
    addresses_layer.addTo(map);
  }

  fit() {
    map.fitBounds(L.geoJSON(addresses).getBounds());
  }
}

export default AddAddressesToMap;