<h2>Map</h2>
<div class="rows">
<div id="coords">?</div>
<div id="cities">
	<span class="result"></span> (<span class="nb_hits"></span> cities, <span class="took"></span> ms)
</div>
	<div>
		<img id="map" src="images/france.svg" />
	</div>
</div>

<script>
$(document).ready(function(){
	$("#map").click(function(e){
	   var parentOffset = $(this).parent().offset();
	   //or $(this).offset(); if you really just want the current element's offset
	   var x = e.pageX - parentOffset.left;
	   var y = e.pageY - parentOffset.top;
	   var lat = getLatitude(y);
	   var lon = getLongitude(x);
	   $("#coords").html('X = '+x+' Y = '+y+' -- lat = '+lat+'  lon = '+lon);
	   retrieveCities(lat, lon);
	});
});

function getLatitude(y) {
	y_brest = 270; lat_brest = 48.4;
	y_nice = 738; lat_nice = 43.70313;
	return getInterpol(y,y_brest,y_nice,lat_brest,lat_nice);
}

function getLongitude(x) {
	x_brest = 64; lon_brest = -4.48333;
	x_nice = 1248; lon_nice = 7.26608;
	return getInterpol(x,x_brest,x_nice,lon_brest,lon_nice);
}

function getInterpol(t,t0,t1,z0,z1) {
	a = (z1-z0)/(t1-t0);
	b = z0 - a*t0;
	return a*t+b;
}


function retrieveCities(lat, lon) {
	var url = 'map_ajax/test/' + lat + '/' + lon;
	$.ajax({
		url:url,
		dataType:'json',
		success: cities_process_data,
		error:function(data){
			cities_set_results(0, [], 'error');//alert('error');
		}
	});
}

function cities_process_data(data) {
	cities = [];
	took = '?';//data['took'];
/*	data['hits']['hits'].forEach(
		function(entry){
			cities.push(entry['_source']['name']+' ');
		}
	);
	cities_set_results(data['hits']['total'], cities, took);
	*/
	data.forEach(
		function(entry){
			cities.push(entry['name']+' ');
		}
	);
	cities_set_results(data.length, cities, took);
}

function cities_set_results(nb, cities, took) {
	$("#cities .nb_hits").html(nb);
	$("#cities .took").html(took);
	$("#cities .result").html(cities);
	console.log(cities);
}

</script>