<div class="cart-total row">
	<div class="col xs-12 sm-8 md-6 lg-4 center">
		<div class="total-line-items">
			<? foreach ($totals as $total) { ?>
				<div class="line-item">
					<div class="title"><?= $total['title']; ?>:</div>
					<div class="text"><?= format('currency', $total['amount']); ?></div>
				</div>
			<? } ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	var reload_totals = function() {
		$totals = $('.cart-total');

		$totals.loading();

		$.get("<?= site_url("block/cart/total/build"); ?>", {}, function(response) {
			$totals.replaceWith(response);
		}, 'html');
	}

	$('body').on('reload_totals', reload_totals);
</script>
