<h2>Posts</h2>

<table class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th>Id</th>
			<th>From</th>
			<th>To</th>
			<th>Body</th>
			<th>Date</th>
			<th>#1</th>
			<th>#2</th>
			<th>#3</th>
			<th><span class="glyphicon glyphicon-picture"></span></th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($posts as $p):?>
		<tr id="row<?php echo $p->pk_post_id; ?>">
			<?php $this->load->view('sub/posts_table_row', array('p' => $p)); ?>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>

