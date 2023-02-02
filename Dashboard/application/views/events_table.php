<h2>Posts</h2>

<table class="datatable table table-striped table-bordered table-nonfluid">
	<thead>
		<tr>
			<th>Id</th>
			<th>From</th>
			<th>To</th>
			<th>Post</th>
			<th>Type</th>
			<th>Date</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($events as $e):?>
		<tr>
			<?php $this->load->view('sub/events_table_row', array('e' => $e)); ?>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>

