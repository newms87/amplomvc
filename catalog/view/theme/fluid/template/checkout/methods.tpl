<div id="shipping-method" class="col xs-12 md-6">
	<? if (!empty($shipping_key)) { ?>
		<div class="method-list">
			<h3><?= _l("Delivery Method"); ?></h3>

			<? $method_format = function ($a) {
				return 'hurray';
			}; ?>

			<? $build = array(
				'name'   => 'shipping_code',
				'data'   => format_all($method_format, $shipping_methods),
				'select' => $shipping_key,
				'key'    => 'code',
				'value'  => 'formatted',
			); ?>

			<?= build('ac-radio', $build); ?>
		</div>
		<a class="change-method"><?= _l("Change Method"); ?></a>
	<? } else { ?>
		<? if ($shipping_key) { ?>
			<div class="error"><?= _l("There are no available Delivery Methods for your order! Please contact <a href=\"mailto:%s\">Customer Support</a> to complete your order.", option('config_email')); ?></div>
		<? } else { ?>
			<div class="notify"><?= _l("Please select your Delivery Address"); ?></div>
		<? } ?>
	<? } ?>
</div>


<div id="payment-method" class="col xs-12 md-6">
	<? if (!empty($payment_methods)) { ?>
		<div class="method-list">
			<h3><?= _l("Payment Method"); ?></h3>

			<? $method_format = function ($a) {
				return call('extension/payment/' . $a['code'], null);
			}; ?>

			<? $build = array(
				'name'   => 'payment_code',
				'data'   => format_all($method_format, $payment_methods),
				'select' => $payment_key,
				'key'    => 'code',
				'value'  => 'formatted',
			); ?>

			<?= build('ac-radio', $build); ?>
		</div>
		<a class="change-method"><?= _l("Change Method"); ?></a>
	<? } else { ?>
		<? if ($payment_key) { ?>
			<div class="error"><?= _l("There are no available Payment Methods for your order! Please contact <a href=\"mailto:%s\">Customer Support</a> to complete your order.", option('config_email')); ?></div>
		<? } else { ?>
			<div class="notify"><?= _l("Please select your Billing Address"); ?></div>
		<? } ?>
	<? } ?>
</div>
