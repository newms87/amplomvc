<div id="braintree-card-select">
	<? if (empty($cards)) { ?>
		<h3><?= _l("Please register a card."); ?></h3>
		<a class="register-card button subscribe" href="<?= $register_card; ?>"><?= _l("Add Card"); ?></a>
	<? } else { ?>
		<div id="braintree-card-list" class="card-list noselect">
			<input type="hidden" name="payment_code" value="braintree"/>

			<? $card_format = function ($card, $key) {
				if ($key === 'new') {
					return call('extension/payment/braintree/register_card', $card['settings']);
				}

				return <<<HTML
				<div class="card">
					<div class="card-type">
						<img src="$card[image]" alt="$card[type]"/>
					</div>
					<div class="name">$card[name]</div>
					<div class="number">$card[masked]</div>
				</div>
HTML;
			}; ?>

			<? $build = array(
				'name'   => 'payment_key',
				'data'   => format_all($card_format, $cards),
				'select' => $payment_key,
				'key'    => 'id',
				'value'  => 'formatted',
			); ?>

			<?= build('ac-radio', $build); ?>
		</div>
	<? } ?>
</div>
