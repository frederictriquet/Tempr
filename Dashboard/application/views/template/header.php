<!doctype html>

<html lang="fr">
	<head>
		<link rel="icon" type="image/png" href="images/favicon-32.png" sizes="32x32" />
  		<link rel="icon" type="image/png" href="images/favicon-16.png" sizes="16x16" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta charset="utf-8" />
		<meta name="Description" content="<?php echo $meta_description?>" />
		<meta name="keywords" content="<?php echo $meta_keywords?>" />
		<title><?php echo $title?></title>

		<?php if(isset($css)):?>
			<?php foreach($css as $src):?>
			<?php if (strpos($src, '//') === 0 ):?>
			<link href="<?php echo $src;?>" rel="stylesheet" type="text/css" />
			<?php else:?>
			<link href="<?php echo base_url()?>css/<?php echo $src;?>" rel="stylesheet" type="text/css" />
			<?php endif;?>
			<?php endforeach;?>
		<?php endif; ?>

		<script type="text/javascript">
                        var site_url = '<?php echo site_url(); ?>';
                        var base_url = '<?php echo base_url(); ?>';
                        var current_url = '<?php echo current_url(); ?>';
		</script>
		<?php if(isset($js)):?>
			<?php foreach($js as $src):?>
			<script type="text/javascript" src="<?php echo base_url()?>js/<?php echo $src;?>"></script>
			<?php endforeach;?>
		<?php endif; ?>
  		 <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  	  	 <script src="https://code.highcharts.com/highcharts.js"></script>
	</head>
	<body>
		<div class="container-fluid" id="top">
			<?php foreach($top as $item):?>
				<?php $this->load->view($item)?>
			<?php endforeach;?>
		</div>
<!-- end of header.php -->
