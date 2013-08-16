<div class="buttons">
	<div class="right">
		<input type="button" value="<?= $button_confirm; ?>" id="button-confirm" class="button"/>
	</div>
</div>
<script type="text/javascript">
	//<!--
	$('#button-confirm').bind('click', function () {
		$.ajax({
			type: 'GET',
			url: "<?= HTTP_CATALOG . "index.php?route=payment/free_checkout/confirm"; ?>",
			success: function () {
				location = '<?= $continue; ?>';
			}
		});
	});
	//--></script>
