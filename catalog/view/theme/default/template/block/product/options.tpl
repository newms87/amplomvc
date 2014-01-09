<div id="product_options" class="options">
	<h2><?= _l("Available Options"); ?></h2>
	<br/>
	<?= $this->builder->setConfig('product_option_value_id', 'value'); ?>

	<? foreach ($product_options as $product_option) { ?>
		<div class="product_option" data-po-id="<?= $product_option['product_option_id']; ?>">
			<? if ($product_option['required']) { ?>
				<span class="required"></span>
			<? } ?>
			<span class="name"><?= $product_option['display_name']; ?>:</span><br/>

			<? switch ($product_option['type']) {
				case 'select':
					?>
					<?= $this->builder->build('select', $product_option['product_option_values'], "options[$product_option[product_option_id]]"); ?>
					<? break;

				case 'radio':
					?>
					<?= $this->builder->build('radio', $product_option['product_option_values'], "options[$product_option[product_option_id]]"); ?>
					<? break;

				case 'checkbox':
					?>
					<?= $this->builder->build('checkbox', $product_option['product_option_values'], "options[$product_option[product_option_id]]"); ?>
					<? break;

				case 'image':
					?>
					<div class="option_image_list">
						<? foreach ($product_option['product_option_values'] as $product_option_value) { ?>
							<? $id = "product_option_image_$product_option_value[product_option_value_id]"; ?>
							<input style="display:none" type="radio" id="<?= $id; ?>" name="options[<?= $product_option['product_option_id']; ?>]" value="<?= $product_option_value['product_option_value_id']; ?>"/>
							<label for="<?= $id; ?>" class="option_image">
								<div class="option_image_box">
									<? if ($product_option_value['thumb']) { ?>
										<a title="<?= $product_option_value['value']; ?>"
										   rel="<?= $product_option_value['rel']; ?>">
											<img src="<?= $product_option_value['thumb']; ?>"/>
										</a>
									<? } else { ?>
										<a href="javscript:void(0);" title="<?= $product_option_value['value']; ?>">
											<img src="<?= $no_image; ?>"/>
										</a>
									<? } ?>
								</div>
								<div class="option_image_name"><?= $product_option_value['value']; ?></div>
							</label>
						<? } ?>
					</div>
					<? break;

				default:
					break;
			} ?>
		</div>
	<? } ?>
</div>

<script type="text/javascript">
	$('#product_options input, #product_options select').change(update_option_restrictions);

	var restrictions = <?= '[]'; //json_encode($product_option_restrictions); ?>;
	function update_option_restrictions() {
		return;

		$('#product_options [ov]').removeAttr('disabled').removeClass('disabled');

		$('.selected_option').each(function (index, e) {
			for (var i in restrictions) {
				ov = 0;
				if ($(e).is('select')) {
					ov = parseInt($(e).find('option[value="' + $(e).val() + '"]').attr('ov'));
				}
				else {
					ov = parseInt($(e).attr('ov'));
				}

				if (i == ov) {
					for (var r = 0; r < restrictions[i].length; r++) {
						ele = $('#product_options [ov="' + restrictions[i][r] + '"]');
						if (ele.is('option')) {
							ele.attr('disabled', 1);
						}
						else {
							ele.addClass('disabled');
						}
					}
				}
			}
		});
	}

	$('.option_image').click(function () {
		if ($(this).hasClass('disabled')) return;

		$(this).closest('.option_image_list').find('.option_image').removeClass('selected_option');
		$(this).addClass('selected_option');

		$('#' + $(this).attr('for')).prop('checked', true);

		update_option_restrictions(parseInt($(this).attr('ov')));
	});
</script>
