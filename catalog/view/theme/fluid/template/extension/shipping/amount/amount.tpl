<div class="shipping-amount-form">
	<? $amount_format = function ($a) {
		return $a['title'] . ' - ' . format('currency', $a['cost']);
	} ?>

	<? $build = array(
		'name' => 'shipping_key',
	   'data' => format_all($amount_format, $quotes),
	   'select' => $shipping_key,
	   'key' => 'shipping_key',
	   'value' => 'formatted',
	); ?>

	<?= build('ac-radio', $build); ?>
</div>
