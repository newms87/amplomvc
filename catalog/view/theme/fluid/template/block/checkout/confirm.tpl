<? if (!empty($redirect)) { ?>
	<script type="text/javascript">
		location = "<?= $redirect; ?>";
	</script>

<? } elseif (!empty($totals_only)) { ?>
	<div class="checkout_totals">
		<?= $block_totals; ?>
	</div>

	<div class="payment">
		<?= $payment; ?>
	</div>

<? } else { ?>
	<div class="checkout-template">
		<? if (isset($block_confirm_address)) { ?>
			<?= $block_confirm_address; ?>
		<? } ?>

		<? if (isset($block_cart)) { ?>
			<div class="checkout_cart">
				<?= $block_cart; ?>
			</div>
		<? } ?>

		<? if (isset($block_coupon)) { ?>
			<div class="checkout_coupon">
				<?= $block_coupon; ?>
			</div>
		<? } ?>

		<div id="checkout_details">
			<div class="checkout_totals">
				<?= $block_totals; ?>
			</div>

			<div class="payment">
				<?= $payment; ?>
			</div>
		</div>
	</div>

	<div id="loading_details" style="display:none">
		<div class="loader"></div>
		<span class="loading_message"><?= _l("Please wait while we update your order totals."); ?></span>
	</div>

	<script type="text/javascript">
		$('body').on('coupon_success', function (event, json) {
			load_block($('#checkout_details .checkout_totals'), 'block/cart/total');
		});

		var loading_html = $('#loading_details').remove();

		function handle_ajax_cart_preload(action, data) {
			$('#checkout_details').html(loading_html.show().height($('#checkout_details').height()));
		}

		$('#the_cart').on('cart_loaded', function () {
			//load_block($('#checkout_details .checkout_totals'), 'block/cart/total');
			handle_ajax_cart_load();
		});

		var retry_count = 3;

		function handle_ajax_cart_load() {
			$.get("<?= $reload_totals; ?>", {}, function (html) {
				details = $('#checkout_details').html(html);

				if (!details.find('.payment').length || !details.find('.checkout_totals').length) {
					if (retry_count <= 0) {
						location = "<?= $checkout_url; ?>";
					}
					else {
						retry_count--;
						setTimeout(handle_ajax_cart_load, 200);
					}
				}
			});
		}
	</script>
<? } ?>