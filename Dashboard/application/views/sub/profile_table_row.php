<tr>
<td data-id="<?php echo $u['pk_user_id']?>" class="user_id"><?php echo $u['pk_user_id']?></td>
	<td title="<?php echo $u['firstname'].' '.$u['lastname']?>">
	<a href="<?php echo WWW_TEMPR_ME.'/u/'.$u['login'];?>"><?php echo $u['login']?></a>
	<div id="p<?php echo $u['pk_user_id']?>" style="display: none">
	    <?php
	        if (!empty($u['signup_date'])) echo $u['signup_date'].'<br/>';
	        if (!empty($u['email'])) echo $u['email'].'<br/>';
	        if (!empty($u['phone'])) echo $u['phone'].'<br/>';
	        if (!empty($u['facebook_id'])) echo '<a href="http://facebook.com/'.$u['facebook_id'].'" target="blank">'.$u['facebook_id'].'</a><br/>';
	    ?>
	</div>
	</td>
	<td>
	<span class="glyphicon glyphicon-envelope" style="color: <?php echo ($u['has_mail'])?"green":"lightgrey";?>"></span>
	<span class="glyphicon glyphicon-thumbs-up" style="color: <?php echo (!empty($u['facebook_id']))?"blue":"lightgrey";?>"></span>
	<span class="glyphicon glyphicon-exclamation-sign" style="color: <?php echo ($u['nb_push']>0)?'blue':'lightgrey'?>"></span>
	</td>
	<td>
	<span style="color: <?php echo ($u['phone_confirmed']?'green':'red') ?>">
	<?php echo $u['phone_country']; ?></span>
	</td>
	<td title="<?php echo $u['nb_friends'] .' amis, '.$u['nb_requests'].' demandes envoyées, '.$u['nb_requested'] .' demandes reçues, '.$u['nb_pending_requests'].' demandes en attente'?>">
	    <?php echo $u['nb_friends'] .', '.$u['nb_requests'].', '.$u['nb_requested'].', '.$u['nb_pending_requests']?>
	</td>
	<td title="<?php echo $u['nb_posts_sent'] .' posts envoyés, '.$u['nb_posts_received']. ' posts reçus'?>"><?php echo $u['nb_posts_sent'] .', '.$u['nb_posts_received']?></td>
	<td title="<?php echo $u['likes_sent'].' likes envoyés, '.$u['likes'].' likes reçus'?>"><?php echo $u['likes_sent'].', '.$u['likes']?></td>
	<td><?php echo strtoupper($u['lang'])?></td>
	<td title="<?php echo $u['last_seen']. ' jours écoulés depuis dernière connexion'?>">
	<span style="color: <?php echo $u['last_seen_color'] ?>">
	<?php echo $u['last_seen']; ?></span>
	</td>
</tr>
