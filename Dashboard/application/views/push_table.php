<h2>Push tokens</h2>
<input id="pushtext" value="Yeah baby!" />
<table class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th>Id</th>
			<th>Name</th>
			<th>Token</th>
			<th></th>
		</tr>
	</thead>

	<tbody>
		<?php $id=0; foreach ($push as $p):?>
		<tr>
			<?php if ($id === $p->pk_user_id) {
				echo '<td></td><td></td><td>'.$p->ios_id.'</td><td></td>';
			} else {
				$id = $p->pk_user_id;
				echo '<td>'.$id.'</td><td>'.$p->firstname.' '.$p->lastname.'</td><td>'.$p->ios_id.'</td><td>';
				?>	<a style="cursor: pointer" onclick="sendPush('<?php echo $id;?>');">
					<span class="glyphicon glyphicon-expand"></span>
					</a>
					</td>
				<?php
			}
			?>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>

<script>
function sendPush(x) {
	var theBody = $('#pushtext').val();
	//$.toast('sending '+theBody+' to '+x, {type:'success'});
	$.post(site_url+'push/ajax_send/'+x, {body: theBody}, function() {
		$.toast('<h4>OK</h4>', {type:'success'});
	})
	.fail(function() {
		$.toast('<h4>ERROR</h4>', {type:'danger'});
	});
}
</script>