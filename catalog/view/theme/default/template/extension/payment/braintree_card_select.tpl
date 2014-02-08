<div id="braintree_card_select">
	<? if (empty($cards)) { ?>
		<h3><?= _l("Please register a card."); ?></h3>
		<a class="register_card button subscribe" href="<?= $register_card; ?>"><?= _l("Add Card"); ?></a>
	<? } else { ?>
		<div id="braintree_card_list" class="card_list noselect">
			<input type="hidden" name="payment_code" value="braintree"/>
			<? foreach ($cards as $card) { ?>
				<div class="card <?= !empty($card['default']) ? 'checked' : ''; ?>">
					<div class="card_type">
						<? if (!empty($card['image'])) { ?>
							<img class="image" src="<?= $card['image']; ?>" alt="<?= $card['type']; ?>"/>
						<? } else { ?>
							<span class="type"><?= $card['type']; ?></span>
						<? } ?>
					</div>
					<div class="name"><?= $card['name']; ?></div>
					<div class="number"><?= $card['masked']; ?></div>
					<input type="radio" name="payment_key" value="<?= $card['id']; ?>" <?= !empty($card['default']) ? 'checked="checked"' : ''; ?> />
					<? if (!empty($card['remove'])) { ?>
						<a href="<?= $card['remove']; ?>" class="remove"></a>
					<? } ?>
				</div>
			<? } ?>
			<a class="new_card add_slide" href="<?= $register_card; ?>"><?= _l("Add Card"); ?></a>
		</div>
	<? } ?>
</div>

<script type="text/javascript">
	$('#braintree_card_list').ac_radio().ac_slidelist({max_rows: 3, x_dir: -1, pad_y: -18});

	$('.new_card, .register_card').click(function () {
		return colorbox($(this));
	});
</script>
