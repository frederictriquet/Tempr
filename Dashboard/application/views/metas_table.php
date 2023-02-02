<h2>Meta data</h2>

<?php if ($is_editing):?>
<a class="btn btn-default" href="<?php echo site_url('metas')?>">Done</a>
<?php else:?>
<a class="btn btn-default" href="<?php echo site_url('metas/edit')?>">Update</a>
<?php endif;?>

<table class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th>id</th>
			<th>Name</th>
			<th>String value</th>
			<th>Integer value</th>
			<th>Date value</th>
			<th>Modification date</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($metas as $p):?>
		<tr id="row<?php echo $p->pk_meta_id; ?>">
			<?php $this->load->view('sub/metas_table_row', array('p' => $p)); ?>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>

<script>
function updateMetaString(id, value) {
	$.get(site_url+'metas/ajax_update/'+id+'/string_val/'+value, function() {
		$.toast('<h4>ok</h4> metas['+id+'].string_val='+value, {type:'success'});
	})
	.fail(function() {
		$.toast('<h4>ERROR</h4> metas['+id+'].string_val='+value, {type:'danger'});
	});
}
function updateMetaInt(id, value) {
	$.get(site_url+'metas/ajax_update/'+id+'/int_val/'+value, function() {
		$.toast('<h4>ok</h4> metas['+id+'].int_val='+value, {type:'success'});
	})
	.fail(function() {
		$.toast('<h4>ERROR</h4> metas['+id+'].int_val='+value, {type:'danger'});
	});
}
function updateMetaDate(id, value) {
	$.get(site_url+'metas/ajax_update/'+id+'/date_val/'+value, function() {
		$.toast('<h4>ok</h4> metas['+id+'].date_val='+value, {type:'success'});
	})
	.fail(function() {
		$.toast('<h4>ERROR</h4> metas['+id+'].date_val='+value, {type:'danger'});
	});
}
</script>