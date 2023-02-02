<!-- begin of main.php -->
<div class="container-fluid" id="main">
	<?php foreach($main as $item):?>
		<?php $this->load->view($item)?>
	<?php endforeach;?>
</div>
<!-- end of main.php -->
