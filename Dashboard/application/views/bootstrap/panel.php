<div
<?php
if (isset($panel_id))
	echo 'id="'.$panel_id.'"';
?>
class="panel panel-default">
<?php if(isset($panel_heading)):?>
<div class="panel-heading"><?php echo $panel_heading;?></div>
<?php endif;?>
<div class="panel-body">
<?php echo $panel_body;?>
</div>
<?php if(isset($panel_footer)):?>
<div class="panel-footer"><?php echo $panel_footer;?></div>
<?php endif;?>
</div>
