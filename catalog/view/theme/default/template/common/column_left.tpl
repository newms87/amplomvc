<? if ($modules || $blocks) { ?>
<div id="column_left">
	<? foreach ($modules as $module) { ?>
	<?= $module; ?>
	<? } ?>
	
	<? foreach($blocks as $block){ ?>
		<?= $block; ?>
	<? } ?>
</div>
<? } ?>
