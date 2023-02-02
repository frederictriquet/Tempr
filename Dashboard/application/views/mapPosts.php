<h2>Localisation des Posts</h2>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAAhxDE46Ea5-CupawChrlGfykuCFe9p64&libraries=visualization"></script>
<?php if(false): ?>
<div id="map" style="width:1200px;height:800px;margin-top:50px;margin-left:auto;margin-right:auto;"></div>
<input id="size">

<script>
var map;

function initialize() {
  var mapOptions = {
    zoom: 9,
    center: {lat: 50.660, lng: 3.08080},
    clickable:true,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
	map = new google.maps.Map(document.getElementById('map'),
      mapOptions);

	$.get(site_url+'MapPosts/ajax_posts/', function(data) {
		data = eval(data);
		console.log(data.length);
		var totalSize = 0;
		for (var row = 0; row < data.length; row++)  
		     totalSize += data[row]['count'];
		for(var cpt = 0; data[cpt] != null; cpt ++) {
	    	  var cityCircle = new google.maps.Circle({
	    	      strokeColor: '#FF6666',
	    	      strokeOpacity: 0.8,
	    	      strokeWeight: 2,
	    	      fillColor: '#FF6666',
	    	      fillOpacity: 0.35,
	    	      map: map,
	    	      center: new google.maps.LatLng(data[cpt]['latitude'], data[cpt]['longitude']),
	    	      radius: ((data[cpt]['count'] / totalSize) * 100) * 1000
	             });
	          createClickableCircle(map, cityCircle, data[cpt]['locality']);
	      }
    });
}
function createClickableCircle(map, circle, info){
    var infowindow =new google.maps.InfoWindow({
         content: info
     });
     google.maps.event.addListener(circle, 'click', function(ev) {
         infowindow.setPosition(circle.getCenter());
         infowindow.open(map);
     });
}
google.maps.event.addDomListener(window, 'load', initialize);
</script>
<?php endif;?>
<style>
.floating-panel {
  position: absolute;
  left: 25%;
  z-index: 5;
  background-color: #fff;
  padding: 5px;
  border: 1px solid #999;
  text-align: center;
  font-family: 'Roboto','sans-serif';
  line-height: 30px;
  padding-left: 10px;
}
</style>
<?php if (false):?>
<div>
    <div class="floating-panel">
          <button onclick="toggleHeatmap()">Toggle Heatmap</button>
          <button onclick="changeRadius()">Change radius</button>
          <button onclick="changeGradient()">Change gradient</button>
    </div>
    <div id="map2" style="width:1200px;height:800px;margin-left:auto;margin-right:auto;"></div>
</div>

<script>
var map2;
var data2;
function toggleHeatmap() {
	  heatmap.setMap(heatmap.getMap() ? null : map2);
	}
function changeRadius() {
	  heatmap.set('radius', heatmap.get('radius') ? null : 20);
	}
function changeGradient() {
  var gradient = [
    'rgba(0, 255, 255, 0)',
    'rgba(0, 255, 255, 1)',
    'rgba(0, 191, 255, 1)',
    'rgba(0, 127, 255, 1)',
    'rgba(0, 63, 255, 1)',
    'rgba(0, 0, 255, 1)',
    'rgba(0, 0, 223, 1)',
    'rgba(0, 0, 191, 1)',
    'rgba(0, 0, 159, 1)',
    'rgba(0, 0, 127, 1)',
    'rgba(63, 0, 91, 1)',
    'rgba(127, 0, 63, 1)',
    'rgba(191, 0, 31, 1)',
    'rgba(255, 0, 0, 1)'
  ]
  heatmap.set('gradient', heatmap.get('gradient') ? null : gradient);
}

function retrievePoints() {
	data2 = Array();
    $.get(site_url+'MapPosts/ajax_posts/', function(data) {
    	var data = eval(data);
    	console.log(data.length);
    	
    	data.forEach(function (e) {
    		data2.push({location: new google.maps.LatLng(e['latitude'], e['longitude']), weight: e['count']});
    	});
    });
}
function initialize2() {
  var mapOptions = {
    zoom: 5,
    center: {lat: 46.824340619658116, lng: 2.4631117229729727},
    clickable:true,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
	map2 = new google.maps.Map(document.getElementById('map2'),
      mapOptions);

	retrievePoints();
	heatmap = new google.maps.visualization.HeatmapLayer({
		    data: data2,
		    map: map2
		  });
      }
google.maps.event.addDomListener(window, 'load', initialize2);
</script>

<?php endif;?>


<div>
    <div class="floating-panel">
          <button onclick="toggleHeatmap3()">Toggle Heatmap</button>
          <button onclick="changeRadius3()">Change radius</button>
          <button onclick="changeGradient3()">Change gradient</button>
    </div>
    <div id="map3" style="width:1200px;height:800px;margin-left:auto;margin-right:auto;"></div>
</div>

<script>
var map3;
var data3;
function toggleHeatmap3() {
	  heatmap3.setMap(heatmap3.getMap() ? null : map3);
	}
function changeRadius3() {
	  heatmap3.set('radius', heatmap3.get('radius') ? null : 20);
	}
function changeGradient3() {
  var gradient = [
    'rgba(0, 255, 255, 0)',
    'rgba(0, 255, 255, 1)',
    'rgba(0, 191, 255, 1)',
    'rgba(0, 127, 255, 1)',
    'rgba(0, 63, 255, 1)',
    'rgba(0, 0, 255, 1)',
    'rgba(0, 0, 223, 1)',
    'rgba(0, 0, 191, 1)',
    'rgba(0, 0, 159, 1)',
    'rgba(0, 0, 127, 1)',
    'rgba(63, 0, 91, 1)',
    'rgba(127, 0, 63, 1)',
    'rgba(191, 0, 31, 1)',
    'rgba(255, 0, 0, 1)'
  ]
  heatmap3.set('gradient', heatmap3.get('gradient') ? null : gradient);
}

function retrievePoints3(bounds = null) {
	if (bounds) {
		// longitude min, longitude max, latitude min, latitude max
		box = bounds.b.b +'/'+bounds.b.f +'/'+bounds.f.b +'/'+bounds.f.f +'/';
	} else
		box = '';
	data3 = Array();
    $.get(site_url+'MapPosts/ajax_posts3/' + box, function(data) {
    	var data = eval(data);
    	console.log(data.length);

    	data.forEach(function (e) {
    		data3.push({location: new google.maps.LatLng(e['latitude'], e['longitude'])});
    	});
        $.toast(data3.length);
        heatmap3.setData(data3);
    });
}
function initialize3() {
  var mapOptions = {
    zoom: 5,
    center: {lat: 46.824340619658116, lng: 2.4631117229729727},
    clickable:true,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
	map3 = new google.maps.Map(document.getElementById('map3'),
      mapOptions);

	heatmap3 = new google.maps.visualization.HeatmapLayer({
//		    data: data3,
		    map: map3
		  });
//	retrievePoints3();
	map3.addListener('tilesloaded', function() {
	    b = map3.getBounds();
	    console.log(b.b);
	    console.log(b.f);
	    retrievePoints3(b);
	});
}
google.maps.event.addDomListener(window, 'load', initialize3);

</script>