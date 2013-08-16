<form action="<?= $action; ?>" method="post">
	<input type="hidden" name="operation_xml" value="<?= $xml; ?>">
	<input type="hidden" name="signature" value="<?= $signature; ?>">

	<div class="buttons">
		<div class="right">
			<input type="submit" value="<?= $button_confirm; ?>" class="button"/>
		</div>
	</div>
</form>
