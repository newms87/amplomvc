<div class="block-cart-coupon">
	<form action="<?= $action; ?>" class="coupon-form form" method="post">
		<div class="form-item coupon-code">
			<input id="coupon-code" type="text" name="coupon_code" placeholder="<?= _l("Enter Code"); ?>" value=""/>
			<button data-loading="<?= _l("Applying..."); ?>"><?= _l("Apply Coupon"); ?></button>
		</div>
	</form>
</div>

<script type="text/javascript">
	$('.coupon-form').submit(function () {
		var $this = $(this);

		$this.find('button').loading();
		$.post($this.attr('action'), $this.serialize(), function (response) {
			$this.find('button').loading('stop');

			if (response.success) {
				$('body').trigger('reload_totals');
			}

			$this.ac_msg(response);
		}, 'json');

		return false;
	});
</script>
