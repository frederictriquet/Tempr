<!-- begin of footer.php -->
		<div class="container" id="bottom">
			<?php foreach($bottom as $item):?>
				<?php $this->load->view($item)?>
			<?php endforeach;?>
		</div>

	</body>
</html>
<!-- end of footer.php -->
