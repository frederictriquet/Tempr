<?php

$panel_body = <<<'EOD'
<input class="htag form-control"></input>
<span class="nb_hits">0</span> hits (<span class="took">0</span> ms)<br/>
<ul class="result">

</ul>
EOD;

	$param = array(
			'panel_id' => 'htag',
			'panel_heading' => 'Htags',
			'panel_body' => $panel_body,
			);
	$this->load->view('bootstrap/panel', $param);
?>

<script>
$(document).ready(function(){
	$('#htag input.htag').on('input',htag_input_change);
});

function htag_input_change() {
	console.log($('#htag input.htag').val());
	var url = 'htags_ajax/test/' + $('#htag input.htag').val();
	$.ajax({
		url:url,
		dataType:'json',
		success: htag_process_data,
		error:function(data){
			console.log(data);
			htag_set_results(0, [], data['took']);//alert('error');
		}
	});
}

function htag_process_data(data) {
	console.log(data);
	words = [];
	took = data['took'];
	data['hits']['hits'].forEach(
		function(entry){
			console.log(entry['_source']['tag']);
			words.push('<li>'+entry['_source']['tag']+'</li>');
		}
	);
	htag_set_results(data['hits']['total'], words, took);
	console.log(JSON.stringify(data, null, 4));

}

function htag_set_results(nb, words, took) {
	$("#htag .nb_hits").html(nb);
	$("#htag .took").html(took);
	$("#htag .result").html(words);

}
</script>