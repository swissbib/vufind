$.getJSON("/themes/sbvfrd/js/raw_map_fr.json", function(libraries){
  var mymap = L.map('mapid').setView([46.758548, 8.1], 8);

  L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1Ijoic3dpc3NiaWIiLCJhIjoiY2pyMHIxcm1zMGQzZDQ5cWVld3FhM3R3aiJ9.gtBW4uloJ6JvWIcPG2EzVw', {
    maxZoom: 18,
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
      '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
      'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    id: 'mapbox.streets'
  }).addTo(mymap);

  var markers = L.markerClusterGroup({
    iconCreateFunction: function(cluster) {
      return L.divIcon({ html: '<span class="btn btn-primary"><b>' + cluster.getChildCount() + '</b>&nbsp;&nbsp;<i class="fa fa-institution"></i></span>' });
    }
  });

  var geoJsonLayer = L.geoJson(libraries, {
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