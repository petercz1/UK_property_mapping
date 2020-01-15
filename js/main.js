import './components/app-selectors.js';
import Components from './components/app-checkboxes.js';
import BuildMap from './components/app-map.js';
import MapMain from './map/map-main.js';

let build_map = new BuildMap();
let components = new Components();
build_map.buildMap().then(() => build_map.geoDemo());
components.renderData();
components.check_selector();

let map_main = new MapMain();