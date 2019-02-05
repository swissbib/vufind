var mymap = L.map('mapid').setView([46.758548, 8.3], 8);
L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1Ijoic3dpc3NiaWIiLCJhIjoiY2pyMHIxcm1zMGQzZDQ5cWVld3FhM3R3aiJ9.gtBW4uloJ6JvWIcPG2EzVw', {
  maxZoom: 18,
  attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
    '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
    'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
  id: 'mapbox.streets'
}).addTo(mymap);

var markers = L.markerClusterGroup({
  maxClusterRadius: 70,
  iconCreateFunction: function(cluster) {
    return L.divIcon({ html: '<span class="btn btn-primary"><b>' + cluster.getChildCount() + '</b>&nbsp;&nbsp;<i class="fa fa-institution"></i></span>' });
  }
});

var customIcon = L.icon({
  iconUrl: VuFind.path + '/themes/sbvfrd/images/map-markers/swissbib_dark2s.png',
  shadowUrl: VuFind.path + '/themes/bootstrap3/css/vendor/leaflet/images/marker-shadow.png',
  iconSize: [25.9, 44.9],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41]
});

$.getJSON("/geojson.json", function(libraries){
  var geoJsonLayer = L.geoJson(libraries, {
    pointToLayer: function (feature, latlng) {
      return L.marker(latlng, {icon: customIcon});
    },
    onEachFeature: function (feature, layer) {
      layer.bindPopup(
        '<b>'+feature.properties.label.de+'</b><br>' +
        feature.properties.group_label.de+'<br>' +
        '<a href="'+feature.properties.url.de+'">' + Library_Information + '</a><br>' +
        '<a href="/Search/Results?lookfor=&type=AllFields&filter%5B%5D=institution%3A%22'+feature.properties.bib_code+'%22">' + Search_This_Library +'</a>'
      );
    }
  });

  markers.addLayer(geoJsonLayer);
  mymap.addLayer(markers);
});

$('#locate-button').click(function(){
  mymap.locate({setView: true, maxZoom: 15});
});

