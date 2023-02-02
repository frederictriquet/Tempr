<h2>Jobs</h2>

<?php if ($is_editing):?>
<a class="btn btn-default" href="<?php echo site_url('jobs')?>">Consulter</a>
<?php else:?>
<a class="btn btn-default" href="<?php echo site_url('jobs/edit')?>">Modifier</a>
<?php endif;?>

<table class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th>id</th>
			<th>Job</th>
			<th>Activity</th>
			<th><a href="http://crontab.guru/">Crontab</a><?php if ($is_editing) echo ' Min&nbsp;H&nbsp;DoM&nbsp;M&nbsp;DoW'?></th>
			<th>Last Start</th>
			<th>Last Duration</th>
			<th></th>
			<th><span class="glyphicon glyphicon-status"></span></th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($jobs as $j):?>
		<tr id="row<?php echo $j->pk_job_id; ?>">
			<?php $this->load->view('sub/jobs_table_row', array('j' => $j)); ?>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>

<script>
function updateJob(id_, key_, value_) {
	var obj = { id: id_, key: key_, value: value_ };
	$.ajax({
		url: site_url+'jobs/ajax_update/',
		type: 'post',
		data: obj,
		success: function() {
			$.toast('<h4>ok</h4> jobs['+id_+']['+key_+']='+value_, {type:'success'});
		}
	})
	.fail(function() {
		$.toast('<h4>ERROR</h4> jobs['+id_+']['+key_+']='+value_, {type:'danger'});
	});
}
</script>
