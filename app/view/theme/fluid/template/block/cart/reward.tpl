<div id="reward" class="section">
	<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<?= _l("Points to use (Max %s):", $total_points); ?>
		<input type="text" name="reward" value="<?= $reward; ?>"/>
		<input type="hidden" name="next" value="reward"/>
		&nbsp;
		<input type="submit" value="<?= _l("Apply Points"); ?>" class="button"/>
	</form>
</div>
