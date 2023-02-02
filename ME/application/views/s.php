<body>
	<div class="page">

		<img class="bandeau" src="/images/BandeauTitre2.png" />

		<div class="toi"><?php echo $lang['you'];?></div>

		<div class="apres"><?php echo $lang['by'];?></div>

		<div class="name"><?php echo $posts['from_firstname'].' '.$posts['from_lastname'];?></div>

			<div class="profil">
		<img class="imgprofil" src="<?php if (array_key_exists('url_profile', $posts))
				echo $posts['url_profile'];
			else
				echo "/images/Empty2.png";	?>" />
		</div>

		<div class="encart">
			<div class="tag">
				<div class="hastag">#<?php echo $posts['tag1']; ?></div>
				<div class="likes"><?php echo $posts['pop1']; ?></div>
				<img class="heart" src="/images/CoeurTag.png" />
			</div>

			<div class="tag">
				<div class="hastag">#<?php echo $posts['tag2']; ?></div>
				<div class="likes"><?php echo $posts['pop2']; ?></div>
				<img class="heart" src="/images/CoeurTag.png" />
			</div>

			<div class="tag">
				<div class="hastag">#<?php echo $posts['tag3']; ?></div>
				<div class="likes"><?php echo $posts['pop3']; ?></div>
				<img class="heart" src="/images/CoeurTag.png" />
			</div>
		</div>

		<div class="post">
		<?php if (array_key_exists('url_media',$posts)):?>
			<img class="imgpost" src="<?php echo $posts['url_media']; ?>" />
		<?php endif;?>
		</div>

		<div class="rejoins"><?php echo $lang['join'];?></div>

		<div class="appstore">
		    <a href="https://itunes.apple.com/WebObjects/MZStore.woa/wa/viewSoftware?<?php echo $tracking;?>id=1125381760&mt=8">
			<img class="imgappstore" src="<?php echo $appstore;?>" />
		    </a>
		</div>

	</div>

</body>