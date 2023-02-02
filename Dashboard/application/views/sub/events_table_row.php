<td><?php echo $e->pk_event_id; ?></td>
<td><?php echo $e->from_fk_user_id;?></td>
<td><a href="/Events/index/<?php echo $e->to_fk_user_id;?>"><?php echo $e->to_fk_user_id;?></a></td>
<td><?php echo $e->fk_post_id;?></td>
<td><?php echo $e->type;?></td>
<td><?php echo $e->creation_ts;?></td>
