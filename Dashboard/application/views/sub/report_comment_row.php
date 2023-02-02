<tr>
<td><?php echo $r->firstname.' '.$r->lastname.' '?><a href="<?php echo WWW_TEMPR_ME.'/u/'.$r->login;?>" target="_blank"><?php echo $r->login?></a></td>
<td><?php echo $r->from_firstname.' '.$r->from_lastname.' '?><a href="<?php echo WWW_TEMPR_ME.'/u/'.$r->from_login;?>" target="_blank"><?php echo $r->from_login?></a></td>
<td><?php echo $r->fk_comment_id;?></td>
<td><?php echo $r->body; ?></td>
<td><?php echo $r->ts;?></td>
<td><button onclick="validate(<?php echo '\'comment\''.','.$r->fk_comment_id.','.$r->from_fk_user_id; ?>)">
		<span class="glyphicon glyphicon-ok" style="color: green"></span>
	</button></td>
<td><button onclick="refuse(<?php echo '\'comment\''.','.$r->fk_comment_id; ?>)">
		<span class="glyphicon glyphicon-remove" style="color: red"></span>
	</button></td>
</div></td>
</tr>