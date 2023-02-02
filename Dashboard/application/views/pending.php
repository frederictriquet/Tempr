
<div class="panel panel-default floatingblock">
    <div class="panel-body">
    <h2>Pending Users</h2>
	<table class="datatable table table-striped table-bordered table-nonfluid">
		<thead>
			<tr>
				<th>Id</th>
				<th><span class="glyphicon glyphicon-earphone"</span></th>
				<th><span class="glyphicon glyphicon-thumbs-up"</span></th>
		</thead>
		<tbody>
		<?php foreach ( $users as $u ):?>
		<tr>
			<td><?php echo $u->pk_user_id?></td>
			<td><?php echo $u->phone?></td>
			<td><?php echo $u->facebook_id?></td>
		</tr>
		<?php endforeach;?>
		</tbody>
	</table>
    </div>
</div>


<div class="panel panel-default floatingblock">
    <div class="panel-body">
    <h2>Pending Friendships</h2>
	<table class="datatable table table-striped table-bordered table-nonfluid">
		<thead>
			<tr>
				<th>From</th>
				<th>To</th>
				<th>Date</th>
		</thead>
		<tbody>
		<?php foreach ( $friends as $f ):?>
		<tr>
				<td><?php echo $f->from_fk_user_id?></td>
				<td><?php echo $f->to_fk_pending_user_id?></td>
				<td><?php echo $f->request_ts?></td>
		</tr>
		<?php endforeach;?>
		</tbody>
	</table>
    </div>
</div>


<div class="panel panel-default floatingblock">
    <div class="panel-body">
    <h2>Pending Posts</h2>
	<table class="datatable table table-striped table-bordered table-nonfluid">
		<thead>
			<tr>
				<th>From</th>
				<th>To</th>
				<th>To Pending</th>
				<th>Date</th>
				<th>Media</th>
		</thead>
		<tbody>
		<?php foreach ( $posts as $p ):?>
		<tr>
			<td><?php echo $p->from_fk_user_id?></td>
			<td><?php echo $p->to_fk_user_id?></td>
			<td><?php echo $p->to_fk_pending_user_id?></td>
			<td><?php echo $p->creation_ts?></td>
			<td><?php if($p->fk_media_id):?> <span class="glyphicon glyphicon-camera"</span>
			<?php elseif($p->fk_media_vid_id):?> <span class="glyphicon glyphicon-facetime-video"</span>
			<?php endif;?></td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
    </div>
</div>
