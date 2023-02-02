<?php if (!isset($is_editing)) $is_editing = false;?>
<td><?php echo $j->pk_job_id; ?></td>
<td title="<?php echo $j->descr; ?>"><?php echo $j->name; ?></td>

<td><?php if ($is_editing) {
  echo '<div class="btn-group" data-toggle="buttons">';
  foreach ($activity_types as $at) {
    echo '<label class="btn btn-default';
    if ($j->activity == $at)
    	echo ' active';
    echo '"><input type="radio" value="'
        . $at
        . '" onchange="updateJob('.$j->pk_job_id.',\'activity\', \''.$at.'\');" />'
        . $at
        . '</label>';
  }
  echo '</div>';
} else {
  echo $j->activity;
}
?>
</td>

<?php
if ($is_editing) echo '<td><input value="'.$j->crontab.'" onchange="updateJob('.$j->pk_job_id.', \'crontab\' ,this.value);" /></td>';
else echo '<td title="'.$j->crontab.'"><code>'.$j->crontab.'</code></td>';
?>

<td><?php echo $j->last_begin_ts;?></td>
<td class="text-right"><?php echo $this->tools->seconds_humanreadable($j->last_duration);?></td>
<td><?php echo $j->is_running?'Running':'Idle';?></td>
<td><?php if (!empty($j->status)):?>
<a style="cursor: pointer" onclick="updateJob(<?php echo $j->pk_job_id;?>,'status', '');$(this.parentElement).html('');"><span class="glyphicon glyphicon-check"></span></a>
<?php echo $j->status;?>
<?php endif;?>
</td>
