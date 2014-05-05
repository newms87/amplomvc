<div class="my_networks">
	<? foreach ($networks as $n) { ?>
		<a target="_blank" href="<?= $n['href']; ?>"><img class="network_icon" src="<?= $n['thumb']; ?>"/></a>
	<? } ?>
</div>