<form action="<?= $action; ?>" method="post">
	<input type="hidden" name="VPSProtocol" value="2.23" />
	<input type="hidden" name="TxType" value="<?= $transaction; ?>" />
	<input type="hidden" name="Vendor" value="<?= $vendor; ?>" />
	<input type="hidden" name="Crypt" value="<?= $crypt; ?>" />
	<div class="buttons">
		<div class="right">
			<input type="submit" value="<?= $button_confirm; ?>" class="button" />
		</div>
	</div>
</form>
