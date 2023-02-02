<h2>Logged in users</h2>

<table class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th>Id</th>
			<th>Token</th>
			<th>Until</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($users as $u):?>
		<tr>
			<td><?php echo $u->fk_user_id;?></td>
			<td><?php echo $u->token;?></td>
			<td><?php echo $u->valid_until_ts;?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>

