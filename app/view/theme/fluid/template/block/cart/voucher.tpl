<div id="voucher" class="section">
	<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<?= _l("Enter your gift voucher code here:"); ?>&nbsp;
		<input type="text" name="voucher" value="<?= $voucher; ?>"/>
		<input type="hidden" name="next" value="voucher"/>
		&nbsp;
		<input type="submit" value="<?= _l("Apply Voucher"); ?>" class="button"/>
	</form>
</div>