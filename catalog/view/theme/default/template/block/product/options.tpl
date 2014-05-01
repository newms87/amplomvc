<div class="product-options">
	<?= $this->builder->setConfig('product_option_value_id', 'value'); ?>

	<? foreach ($product_options as $product_option) { ?>
		<? if ($product_option['type'] === 'radio') { ?>
			<? $product_option['type'] = 'ac-radio'; ?>
		<? } ?>

		<div class="product-option form-item" data-po-id="<?= $product_option['product_option_id']; ?>">
			<? if ($product_option['required']) { ?>
				<span class="required"></span>
			<? } ?>
			<span class="name"><?= $product_option['display_name']; ?></span><br/>

			<? switch ($product_option['type']) {
				case 'select':
				case 'ac-radio':
				case 'radio':
				case 'checkbox':
					?>
					<?= $this->builder->build($product_option['type'], $product_option['product_option_values'], "options[$product_option[product_option_id]]", $product_option['default']); ?>
					<? break;

				case 'image':
					?>
					<div class="option-image-list">
						<? foreach ($product_option['product_option_values'] as $product_option_value) { ?>
							<? $id = "product_option_image_$product_option_value[product_option_value_id]"; ?>
							<input style="display:none" type="radio" id="<?= $id; ?>" name="options[<?= $product_option['product_option_id']; ?>]" value="<?= $product_option_value['product_option_value_id']; ?>"/>
							<label for="<?= $id; ?>" class="option-image">
								<div class="option-image-box">
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
								<div class="option-image-name"><?= $product_option_value['value']; ?></div>
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
	var $options = $('.product-options');
	$options.find('input, select').change(update_option_restrictions);

	var restrictions = <?= '[]'; //json_encode($product_option_restrictions); ?>;
	function update_option_restrictions() {
		return;

		$options.find('[ov]').prop('disabled', false).removeClass('disabled');

		$('.selected-option').each(function (index, e) {
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
						ele = $options.find('[ov="' + restrictions[i][r] + '"]');
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

	$('.option-image').click(function () {
		var $this = $(this);
		if ($this.hasClass('disabled')) return;

		$this.closest('.option-image-list').find('.option-image').removeClass('selected-option');
		$this.addClass('selected-option');

		$('#' + $this.attr('for')).prop('checked', true);

		update_option_restrictions(parseInt($this.attr('ov')));
	});
</script>
