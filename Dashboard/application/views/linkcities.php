<h2>Link Cities</h2>

<select name="devcities" id="devcities" onchange="change();" multiple size="10">
	<?php foreach ($devcities as $d):?>
		<option value="<?php echo $d->pk_devcity_id;?>">
		<?php echo $d->locality.','.$d->country_code;?> </option>
		<?php endforeach;?>
</select>

<span id="blocCities"></span>

<button onclick="submitcities()">Set</button>

<div >
<span>3lettres,CountryCode <input onchange="manualChange(this.value)" /></span>
<span id="manualBlocCities"></span>
</div>


<script>
function change()
{
	devcity = document.getElementById("devcities").options[document.getElementById('devcities').selectedIndex].text;
	city = devcity.split(',');
	$.get(site_url+'LinkCities/ajax_cities/'+city[0]+'/'+city[1], function(data) {
		data = eval(data);
		var nb = data.length;
	    var form_d  = '<select name="cities" id="cities" size="30" multiple>';
	    
		for(var j = 0;  j < nb; j++) {
	        form_d += '  <option value="'+ data[j]["pk_city_id"] +'">'+ data[j]["name"] +',' + data[j]['country'] + ',' + data[j]["pk_city_id"] + ' (' + data[j]["latitude"] + ',' + data[j]["longitude"]+ ')<\/option>';  
	    }
	    form_d += '<\/select>';
	    document.getElementById("blocCities").innerHTML = form_d;
	    
	});
}
function manualChange(v) {
	city = v.split(',');
	$.get(site_url+'LinkCities/ajax_cities/'+city[0]+'/'+city[1], function(data) {
		data = eval(data);
		var nb = data.length;
	    var form_d  = '<select name="cities" id="cities" size="30" multiple>';
	    
		for(var j = 0;  j < nb; j++) {
	        form_d += '  <option value="'+ data[j]["pk_city_id"] +'">'+ data[j]["name"] +',' + data[j]['country'] + ',' + data[j]["pk_city_id"] + ' (' + data[j]["latitude"] + ',' + data[j]["longitude"]+ ')<\/option>';  
	    }
	    form_d += '<\/select>';
	    document.getElementById("blocCities").innerHTML = form_d;
	});
}

function submitcities() {
	devcity = document.getElementById("devcities").options[document.getElementById('devcities').selectedIndex].value;
	idcity = document.getElementById("cities").options[document.getElementById('cities').selectedIndex].value; 

	$.get(site_url+'LinkCities/ajax_update/'+devcity+'/'+idcity, function() {
		$.toast('<h4>ok</h4> linkcities:'+idcity+'->'+devcity, {type:'success'});
	})
	.fail(function() {
		$.toast('<h4>ERROR</h4> linkcities:'+idcity+'->'+devcity, {type:'danger'});
	});
}
</script>