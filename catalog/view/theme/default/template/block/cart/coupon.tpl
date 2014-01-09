<div id="coupon_block">
	<form action="" method="post" <?= $ajax ? "onclick=\"return apply_coupon();\"" : ''; ?>>
		<span><?= _l("Enter your coupon here:"); ?></span>
		<input id="coupon_code" type="text" name="coupon_code" value=""/>
		<input type="submit" value="<?= _l("Apply Coupon"); ?>" class="button small"/>
	</form>
</div>

<? if ($ajax) { ?>
	<script type="text/javascript">
		function apply_coupon() {
			if ($('#coupon_code').val()) {
				submit_block('coupon', '<?= $ajax_url; ?>', $('#coupon_block form'));
			}

			return false;
		}

		$('body').bind('coupon_success', function () {
			$('input[name=coupon_code]').val('');
		});
		$('body').bind('coupon_error', function () {
		});
</script>
<? } ?>
