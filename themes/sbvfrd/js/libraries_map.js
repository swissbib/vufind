var mymap = L.map('mapid').setView([46.758548, 8.1], 8);

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
  maxZoom: 18,
  attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
    '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
    'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
  id: 'mapbox.streets'
}).addTo(mymap);

var myLayer = L.geoJSON(libraries, {
  pointToLayer: function (feature, latlng) {
    return L.circleMarker(latlng, {
      color: '#fff',
      radius: 7,
      fillColor: feature.properties.color,
      weight: 1,
      opacity: 1,
      fillOpacity: 1
    });
  }
}).bindPopup(function (layer) {
  return layer.feature.properties.Nom;
}).addTo(mymap);



var mymap2 = L.map('mapid-2').setView([46.758548, 8.1], 8);

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
  maxZoom: 18,
  attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
    '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
    'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
  id: 'mapbox.streets'
}).addTo(mymap2);

var myLayer2 = L.geoJSON(libraries, {
  pointToLayer: function (feature, latlng) {
    return L.marker(latlng, {
      color: '#fff',
      radius: 7,
      fillColor: feature.properties.color,
      weight: 1,
      opacity: 1,
      fillOpacity: 1
    });
  }
}).bindPopup(function (layer) {
  return layer.feature.properties.Nom;
}).addTo(mymap2);
//myLayer.addData(libraries);
//myLayer.setStyle(geojsonMarkerOptions);
//
// L.geoJSON(libraries, {
//   style: function (feature) {
//     return {color: feature.properties.marker-color};
//   }
// }).bindPopup(function (layer) {
//   return layer.feature.properties.Nom;
// }).addTo(map);


//https://github.com/Leaflet/Leaflet.markercluster/blob/master/example/geojson.html

var mymap3 = L.map('mapid-3').setView([46.758548, 8.1], 8);

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
  maxZoom: 18,
  attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
    '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
    'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
  id: 'mapbox.streets'
}).addTo(mymap3);

var markers = L.markerClusterGroup();

var geoJsonLayer = L.geoJson(libraries, {
  onEachFeature: function (feature, layer) {
    layer.bindPopup(feature.properties.Nom);
  }
});

markers.addLayer(geoJsonLayer);
mymap3.addLayer(markers);

var mymap4 = L.map('mapid-4').setView([46.758548, 8.1], 8);

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
  maxZoom: 18,
  attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
    '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
    'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
  id: 'mapbox.streets'
}).addTo(mymap4);

var markers2 = L.markerClusterGroup();

var geoJsonLayer2 = L.geoJson(libraries, {
  pointToLayer: function (feature, latlng) {
    return L.circleMarker(latlng, {
      color: '#fff',
      radius: 7,
      fillColor: feature.properties.color,
      weight: 1,
      opacity: 1,
      fillOpacity: 1
    });
  },
  onEachFeature: function (feature, layer) {
    layer.bindPopup(feature.properties.Nom);
  }
});

markers2.addLayer(geoJsonLayer2);
mymap4.addLayer(markers2);



