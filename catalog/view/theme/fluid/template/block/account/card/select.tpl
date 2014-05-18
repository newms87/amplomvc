<div class="block-select-card">
	<? if (empty($cards)) { ?>
		<h3><?= _l("Please register a card."); ?></h3>
	<? } else { ?>
		<div class="form-item card-list noselect">
			<input type="hidden" name="payment_code" value="<?= $payment_code; ?>"/>

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

	<div class="form-item add-card">
		<a class="register-card colorbox" href="<?= $register_card; ?>"><?= _l("Add Card"); ?></a>
	</div>

</div>

