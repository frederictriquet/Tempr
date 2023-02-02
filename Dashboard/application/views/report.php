<h2>Report Posts</h2>

<table class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th>Reporter</th>
			<th>Post From</th>
			<th>Post To</th>
			<th>id Post</th>
			<th>Media</th>
			<th>Body</th>
			<th>Tags</th>
			<th>Date</th>
			<th colspan="2">Status</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($report_post as $r)
   		$this->load->view('sub/report_post_row', array('r' => $r));
		?>
	</tbody>
</table>

<h2>Report Comments</h2>

<table class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th>Reporter</th>
			<th>Comment From</th>
			<th>Comment ID</th>
			<th>Body</th>
			<th>Date</th>
			<th colspan="2">Status</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($report_comments as $r)
			$this->load->view('sub/report_comment_row', array('r' => $r));
		?>
	</tbody>
</table>

<script>
function validate(type, id_type, id_user) {
	$.get(site_url+'Report/ajax_validate/'+type+'/'+id_type+'/'+id_user, function() {
		$.toast('<h4>ok</h4> Validate: '+type+' '+id_type, {type:'success'});
	})
	.fail(function() {
		$.toast('<h4>ERROR</h4> Validate : '+type+' '+id_type, {type:'danger'});
	});
}

function refuse(type, id) {
	$.get(site_url+'Report/ajax_refuse/'+type+'/'+id, function() {
		$.toast('<h4>OK</h4> Refuse:'+type+' '+id, {type:'success'});
	})
	.fail(function() {
		$.toast('<h4>ERROR</h4> Refuse:'+type+' '+id_post, {type:'danger'});
	});
}
</script>