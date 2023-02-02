<h2>Parameters</h2>

<?php if ($is_editing):?>
<a class="btn btn-default" href="<?php echo site_url('parameters')?>">Done</a>
<?php else:?>
<a class="btn btn-default" href="<?php echo site_url('parameters/edit')?>">Update</a>
<?php endif;?>

<table class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th>id</th>
			<th>Variable</th>
			<th>Valeur</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($parameter as $p):?>
		<tr id="row<?php echo $p->pk_parameter_id; ?>">
			<?php $this->load->view('sub/parameters_table_row', array('p' => $p)); ?>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>

<script>
function updateParameter(id, value) {
	$.get(site_url+'parameters/ajax_update/'+id+'/'+value, function() {
		$.toast('<h4>ok</h4> conf['+id+']='+value, {type:'success'});
	})
	.fail(function() {
		$.toast('<h4>ERROR</h4> conf['+id+']='+value, {type:'danger'});
	});
}
</script>