<h3>pending posts: destinataire n'a pas validé son phone alors que dans la table users, le phone est là</h3>

<table class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th>pk_post_id</th>
			<th>creation_ts</th>
			<th>from_fk_user_id</th>
			<th>to_fk_user_id</th>
			<th>to_fk_pending_user_id</th>
			<th>fk_devcity_id</th>
			<th>pending_reason</th>
			<th>phone</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($phone as $p):?>
		<tr>
		<td><?php echo $p->pk_post_id;?> </td>
		<td><?php echo $p->creation_ts;?> </td>
		<td><?php echo $p->from_fk_user_id;?> </td>
		<td><?php echo $p->to_fk_user_id;?> </td>
		<td><?php echo $p->to_fk_pending_user_id;?> </td>
		<td><?php echo $p->fk_devcity_id;?> </td>
		<td><?php echo $p->pending_reason;?> </td>
		<td><?php echo $p->phone;?> </td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>

<h3>pending posts: destinataire n'a pas FB alors que dans la table users, il y a FB</h3>
<table class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th>pk_post_id</th>
			<th>creation_ts</th>
			<th>from_fk_user_id</th>
			<th>to_fk_user_id</th>
			<th>to_fk_pending_user_id</th>
			<th>fk_devcity_id</th>
			<th>pending_reason</th>
			<th>facebook_id</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($fb as $f):?>
		<tr>
		<td><?php echo $f->pk_post_id;?> </td>
		<td><?php echo $f->creation_ts;?> </td>
		<td><?php echo $f->from_fk_user_id;?> </td>
		<td><?php echo $f->to_fk_user_id;?> </td>
		<td><?php echo $f->to_fk_pending_user_id;?> </td>
		<td><?php echo $f->fk_devcity_id;?> </td>
		<td><?php echo $f->pending_reason;?> </td>
		<td><?php echo $f->facebook_id;?> </td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>

<h3>pending posts: une attente d'amitié alors qu'il n'y a pas de demande d'amitié</h3>
<table class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th>fk_user_id1</th>
			<th>fk_user_id2</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($friends_requests as $fr):?>
		<tr>
		<td><?php echo $fr->pk_post_id;?> </td>
		<td><?php echo $fr->creation_ts;?> </td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>

<h3>Users seuls avec friendships requests</h3>
<table class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th>pk_user_id</th>
			<th>login</th>
			<th>demandeur</th>
			<th>demandey</th>
			<th>inviteur</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($user_requests as $ur):?>
		<tr>
		<td><?php echo $ur->pk_user_id;?> </td>
		<td><?php echo $ur->login;?> </td>
		<td><?php echo $ur->demandeur;?> </td>
		<td><?php echo $ur->demandey;?> </td>
		<td><?php echo $ur->inviteur;?> </td>
		</tr>
		<?php endforeach;?>	
	</tbody>
</table>

<h3>Users seuls avec pending friendships requests</h3>
<table class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th>from_fk_user_id</th>
			<th>to_fk_pending_user_id</th>
			<th>request_ts</th>
			<th>phone</th>
			<th>facebook_id</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($user_pending as $up):?>
		<tr>
		<td><?php echo $up->from_fk_user_id;?> </td>
		<td><?php echo $up->to_fk_pending_user_id;?> </td>
		<td><?php echo $up->request_ts;?> </td>
		<td><?php echo $up->phone;?> </td>
		<td><?php echo $up->facebook_id;?> </td>
		</tr>
		<?php endforeach;?>	
	</tbody>
</table