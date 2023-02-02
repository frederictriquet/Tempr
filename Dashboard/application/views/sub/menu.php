<div class="navigation">
<a class="btn btn-default" href="<?php echo site_url('home')?>"><span class="glyphicon glyphicon-home"></span></a>
<a class="btn btn-default" href="<?php echo site_url('Stats')?>">Stats</a>
<a class="btn btn-default" href="<?php echo site_url('Profiles')?>">Profiles</a>
<a class="btn btn-default" href="<?php echo site_url('Posts')?>">Posts</a>
<a class="btn btn-default" href="<?php echo site_url('Pending')?>">Pending</a>
<a class="btn btn-default" href="<?php echo site_url('Infos')?>">Infos</a>
<a class="btn btn-default" href="<?php echo site_url('Events')?>">Events</a>
<a class="btn btn-default" href="<?php echo site_url('MapPosts')?>">Map Posts</a>
<a class="btn btn-default" href="<?php echo site_url('Report')?>">Report</a>
<a class="btn btn-default" href="<?php echo site_url('Hashtags')?>">Tags</a>
<?php if ($_SERVER['REMOTE_USER'] === 'rodger'): ?>
|
<a class="btn btn-default" href="<?php echo site_url('Jobs')?>">Jobs</a>
<a class="btn btn-default" href="<?php echo site_url('Push')?>">Push</a>
<!-- <a class="btn btn-default" href="<?php echo site_url('Logged')?>">Logged in</a> -->
<a class="btn btn-default" href="<?php echo site_url('LinkCities')?>">Link Cities</a>
<!-- <a class="btn btn-default" href="<?php echo site_url('Map')?>">Map</a> -->
<a class="btn btn-default" href="<?php echo site_url('parameters')?>">Params</a>
<a class="btn btn-default" href="<?php echo site_url('metas')?>">Metas</a>
|
<!-- <a class="btn btn-default" href="<?php echo site_url('Search')?>">Search</a> -->
<?php endif;?>
<span><?php echo date("G:i:s");?></span>
</div>
<?php if ($_SERVER['REMOTE_USER'] === 'olivier') {
    echo "<hr/><h1>PUTAIN OLIVIER, QU'EST-CE QUE TU FOUS ? MAINTENANT T'ARRETES DE NOUS FILTRER ET TU REPRENDS CONTACT IMMEDIATEMENT</h1><hr/>";
    error_log($_SERVER['REMOTE_USER']);
}
?>