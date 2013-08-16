<div id="voucher" class="section">
	<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<?= $entry_voucher; ?>&nbsp;
		<input type="text" name="voucher" value="<?= $voucher; ?>"/>
		<input type="hidden" name="next" value="voucher"/>
		&nbsp;
		<input type="submit" value="<?= $button_voucher; ?>" class="button"/>
	</form>
</div>