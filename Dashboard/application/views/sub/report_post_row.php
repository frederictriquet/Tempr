<tr>
<td><?php echo $r->firstname.' '.$r->lastname.' '?><a href="<?php echo WWW_TEMPR_ME.'/u/'.$r->login;?>" target="_blank"><?php echo $r->login?></a></td>
<td><?php echo $r->from_firstname.' '.$r->from_lastname.' '?><a href="<?php echo WWW_TEMPR_ME.'/u/'.$r->from_login;?>" target="_blank"><?php echo $r->from_login?></a></td>
<td><?php echo $r->to_firstname.' '.$r->to_lastname.' '?><a href="<?php echo WWW_TEMPR_ME.'/u/'.$r->to_login;?>" target="_blank"><?php echo $r->to_login?></a></td>
<td><?php echo $r->fk_post_id?></td>
<?php if (!empty($r->filename_vid)):?>
<td> <img src="<?php echo $r->filename_vid;?>"/></td>
<?php elseif (!empty($r->filename)): ?>
<td> <img src="<?php echo $r->filename;?>"/></td>
<?php endif; ?>
<td><?php echo $r->body;?></td>
<td><?php if (!empty($r->tag1)) echo $r->tag1.' ';
	      if (!empty($r->tag2)) echo $r->tag2.' ';
	      if (!empty($r->tag3)) echo $r->tag3;?></td>
<td><?php echo $r->ts;?></td>
<td><button onclick="validate(<?php echo '\'post\''.','.$r->fk_post_id.','.$r->from_fk_user_id; ?>)">
		<span class="glyphicon glyphicon-ok" style="color: green"></span>
	</button></td>
<td><button onclick="refuse(<?php echo '\'post\''.','.$r->fk_post_id; ?>)">
		<span class="glyphicon glyphicon-remove" style="color: red"></span>
	</button></td>
	</div></td>
</tr>