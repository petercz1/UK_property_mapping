import RootElement from '../libraries/rootelement.js';

class AppMap extends RootElement {

  constructor() {
    super();
    this.renderData();
  }

  renderData() {
    this.innerHTML = `
	  <div id="map">
	  </div>
    `;
  }

  async buildMap() {
    console.log('building map...');
    window.map = L.map('map');
    // using openstreetmap to supply the map tiles. In Your Face, Google...
    window.tile_layer = await L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      //maxZoom: 18
    });
    tile_layer.addTo(map);
  }

  geoDemo() {
    console.log('loading geoJSON');
    window.geojsonFeatures = [{
      "type": "Feature",
      "properties": {
        "name": "Oxford",
        "popupContent": "This is home!"
      },
      "geometry": {
        "type": "Point",
        "coordinates": [-1.261685, 51.764762]
      }
    }];
    map.fitBounds(L.geoJSON(geojsonFeatures).getBounds());
    L.control.scale().addTo(map);
    window.geoJSON = L.geoJSON(geojsonFeatures);
    geoJSON.addTo(map);
    console.log(map);
  }
}

customElements.define('app-map', AppMap);

export default AppMap;