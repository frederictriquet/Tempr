<body>
	<div class="page">

		<img class="bandeau" src="../../images/BandeauTitre2.png" />

		<div class="profil">
			<img class="imgprofil" src="<?php echo $u->url_profile; ?>" />
		</div>

		<div class="name"> <?php echo $u->firstname.' '.$u->lastname; ?></div>

		<div class="tiret">
		</div>

		<div class="amis"><?php echo $lang['by'];?></div>

		<div class="encart">
		    <?php foreach ( $obj as $o ): ?>
			<div class="tag">
				<div class="hastag"> #<?php echo $o[0]; ?></div>
				<div class="likes"><p><?php echo $o[1]; ?></p></div>
				<img class="heart" src="../../images/CoeurTag.png" />
			</div>
			<?php endforeach; ?>
		</div>

		<div class="appstore">
		    <a href="https://itunes.apple.com/WebObjects/MZStore.woa/wa/viewSoftware?<?php echo $tracking;?>id=1125381760&mt=8">
			<img class="imgappstore" src="<?php echo $appstore;?>" />
		    </a>
		</div>

	</div>
</body>