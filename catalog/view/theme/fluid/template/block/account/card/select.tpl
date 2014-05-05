<div class="block-select-card">
	<? if (empty($cards)) { ?>
		<h3><?= _l("Please register a card."); ?></h3>
	<? } else { ?>
		<div class="form-item card-list noselect">
			<input type="hidden" name="payment_code" value="<?= $payment_code; ?>"/>

			<? array_walk($cards, function (&$c) {
				$c['formatted'] = "<img src=\"$c[image]\" /><span class=\"last-4\">" . _l("Ending in %s", $c['last4']) . "</span>";
			}); ?>

			<? $build = array(
				'data'      => $cards,
				'name'      => 'payment_key',
				'value'     => $payment_key,
				'key_id'    => 'id',
				'key_value' => 'formatted',
			); ?>

			<?= build('ac-radio', $build); ?>
		</div>
	<? } ?>

	<div class="form-item add-card">
		<a class="register-card colorbox" href="<?= $register_card; ?>"><?= _l("Add Card"); ?></a>
	</div>

</div>

