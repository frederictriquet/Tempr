<td><?php echo $p->pk_post_id; ?></td>
<td><?php echo $p->from_fk_user_id;?></td>
<td><a href="/Posts/index/<?php echo $p->to_fk_user_id;?>"><?php echo $p->to_fk_user_id;?></a></td>
<td><?php echo $p->body;?></td>
<td><?php echo $p->creation_ts;?></td>
<td>
	<span><?php echo $p->tag1;?></span>&nbsp;&nbsp;
	<span style="float:right">
		<?php echo $p->pop1;?><span class="glyphicon glyphicon-heart"></span>
	</span>
</td>

<td>
	<span><?php echo $p->tag2;?></span>&nbsp;&nbsp;
	<span style="float:right">
		<?php echo $p->pop2;?><span class="glyphicon glyphicon-heart"></span>
	</span>
</td>

<td>
	<span><?php echo $p->tag3;?></span>&nbsp;&nbsp;
	<span style="float:right">
		<?php echo $p->pop3;?><span class="glyphicon glyphicon-heart"></span>
	</span>
</td>

<td>
	<?php if (!empty($p->filename_vid)):?>
	<span class="glyphicon glyphicon-facetime-video"</span>
	<?php elseif (!empty($p->filename)):?>
	<span class="glyphicon glyphicon-camera"</span>
	<?php endif;?>
</td>