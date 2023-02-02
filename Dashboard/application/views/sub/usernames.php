<?php

$panel_body = <<<'EOD'
<input class="username form-control"></input>
<span class="nb_hits">0</span> hits (<span class="took">0</span> ms)<br/>
<ul class="result">

</ul>
EOD;

	$param = array(
			'panel_id' => 'username',
			'panel_heading' => 'User names',
			'panel_body' => $panel_body,
			);
	$this->load->view('bootstrap/panel', $param);
?>

<script>
$(document).ready(function(){
	$('#username input.username').on('input',username_input_change);
});

function username_input_change() {
	//console.log($('#username input.username').val());
	var url = 'usernames_ajax/test/' + $('#username input.username').val();
	$.ajax({
		url:url,
		dataType:'json',
		success: username_process_data,
		error:function(data){
			//console.log(data);
			username_set_results(0, [], data['took']);//alert('error');
		}
	});
}

function username_process_data(data) {
	//console.log(data);
	words = [];
	took = data['took'];
	data['hits']['hits'].forEach(
		function(entry){
			words.push('<li>'+entry['_source']['firstname']+' '+entry['_source']['lastname']+'</li>');
		}
	);
	username_set_results(data['hits']['total'], words, took);
	console.log(JSON.stringify(data, null, 4));

}

function username_set_results(nb, words, took) {
	$("#username .nb_hits").html(nb);
	$("#username .took").html(took);
	$("#username .result").html(words);

}
</script>