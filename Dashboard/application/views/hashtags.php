<h2>Classement #</h2>

<table
	class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th>Id #</th>
			<th>#</th>
			<th>Likes</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($rank_tags as $r):?>
		<tr>
			<td><?php echo $r->pk_htag_id;?></td>
			<td><?php echo $r->tag;?></td>
			<td><?php echo $r->nb;?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>


<h2>Nombre total de #</h2>

<table
	class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th>#</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo $count_tags[0]->count;?></td>
		</tr>
	</tbody>
</table>

<h2>Nombre total de Likes</h2>

<table
	class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th><span class="glyphicon glyphicon-heart"></span></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo $count_likes[0]->count;?></td>
		</tr>
	</tbody>
</table>