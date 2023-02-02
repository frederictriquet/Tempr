<h2><?php echo $nbUsers; ?> Utilisateurs</h2>
<span class="pagination">
	<?php foreach ($index as $i): ?>
		<a onclick="pagination(<?php echo '\''.$i['page'].'\', \''.$i['display'].'\', \''.$i['limit'].'\', \''.$i['offset'].'\'' ;?>)"
		<?php if ($i['page'] == $currentPage):?>
			class="current"
		<?php endif;?>
		><?php echo $i['display'];?></a>
	<?php endforeach;?>
</span>

<div class="tableaux">
	<table class="datatable table table-striped table-bordered table-nonfluid">
		<thead>
			<tr>
				<th>id</th>
				<th>Login</th>
				<th></th>
				<th><span class="glyphicon glyphicon-earphone"></span></th>
				<th><span class="glyphicon glyphicon-user"></span></th>
				<th><span class="glyphicon glyphicon-tag"></span></th>
				<th><span class="glyphicon glyphicon-heart"></span></th>
				<th><span class="glyphicon glyphicon-flag"></span></th>
				<th><span class="glyphicon glyphicon-log-in"></span></th>
			</tr>
		</thead>
		<tbody id="tbody">
		<?php for($i = 0; $i < sizeof($users); ++ $i)
			$this->load->view ('sub/profile_table_row', array('u' => $users [$i])); ?>
		</tbody>
	</table>
</div>
<div class="pagination">
	<?php foreach ($index as $i): ?>
		<a onclick="pagination(<?php echo '\''.$i['page'].'\', \''.$i['display'].'\', \''.$i['limit'].'\', \''.$i['offset'].'\'' ;?>)"
		<?php if ($i['page'] == $currentPage):?>
			class="current"
		<?php endif;?>
		><?php echo $i['display'];?></a>
	<?php endforeach;?>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$('.user_id').click(function() {
		//$.toast($(this).data('id'), {type:'success'});
		var id = '#p'+$(this).data('id');
		$(id).toggle();
	});
});
function pagination(page, display, limit, offset) {
	$.get(site_url+'Profiles/ajax_pagination/'+page+'/'+display+'/'+limit+'/'+offset, function(data) {
		$("#main").html(data);
	})
}
</script>