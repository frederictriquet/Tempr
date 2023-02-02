<h2>Test RabbitMQ</h2>

<a class="btn btn-default" onclick="mq_send(0)">Send Mail</a>
<a class="btn btn-default" onclick="mq_send(1)">Send SMS</a>

<script>
function mq_send(x) {
	$.get(site_url+'jobs/ajax_mq/'+x, function() {
		$.toast('<h4>OK</h4>', {type:'success'});
	})
	.fail(function() {
		$.toast('<h4>ERROR</h4>', {type:'danger'});
	});
}
</script>