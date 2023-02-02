<div id="container" style="height: 400px; width:400px">
Réinitialisation de mot de passe<br/>
<?php echo $user->firstname . ' ' . $user->lastname; ?><br/>
<form method="POST">
<p class="<?php if (form_error('password')!=''):?>form-error<?php endif;?>">
	<label for="password">Nouveau mot de passe</label>
	<input type="password" name="password" />
	<?php echo form_error('password');?>
</p>

<p class="<?php if (form_error('confirmpassword')!=''):?>form-error<?php endif;?>">
	<label for="confirmpassword">Confirmation mot de passe</label>
	<input type="password" name="confirmpassword" />
	<?php echo form_error('confirmpassword');?>
</p>

<input type="submit" value="Valider"/><br/>
</form>
</div>
