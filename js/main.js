import './components/app-selectors.js';
import Components from './components/app-checkboxes.js';
import BuildMap from './components/app-map.js';
import MapMain from './map/map-main.js';

let build_map = new BuildMap();
build_map.buildMap().then(() => build_map.geoDemo());

let map_main = new MapMain();