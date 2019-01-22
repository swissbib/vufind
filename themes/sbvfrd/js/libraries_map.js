var mymap = L.map('mapid').setView([46.758548, 8.1], 8);

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
  maxZoom: 18,
  attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
    '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
    'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
  id: 'mapbox.streets'
}).addTo(mymap);

var myStyle = {
  "color": "#3d6eff",
  "weight": 5,
  "opacity": 0.65
};

var geojsonMarkerOptions = {
  radius: 7,
  fillColor: "#ff7800",
  color: "#000",
  weight: 1,
  opacity: 1,
  fillOpacity: 1
};

var myLayer = L.geoJSON(libraries, {
  pointToLayer: function (feature, latlng) {
    return L.circleMarker(latlng, geojsonMarkerOptions);
  }
}).bindPopup(function (layer) {
  return layer.feature.properties.Nom;
}).addTo(mymap);
//myLayer.addData(libraries);
//myLayer.setStyle(geojsonMarkerOptions);


