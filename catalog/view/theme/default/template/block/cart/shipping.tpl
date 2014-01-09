<form id="cart_shipping" action="<?= $action; ?>" class="section">
	<p><?= _l("Enter your destination to get a shipping estimate."); ?></p>
	<table>
		<tr>
			<td class="required"> <?= _l("Country:"); ?></td>
			<td>
				<? $this->builder->setConfig('country_id', 'name'); ?>
				<?= $this->builder->build('select', $countries, "country_id", $country_id, array('class' => 'country_select')); ?>
			</td>
		</tr>
		<tr>
			<td class="required"> <?= _l("Region / State:"); ?></td>
			<td><select name="zone_id" class="zone_select" zone_id="<?= $zone_id; ?>"></select></td>
		</tr>
		<tr>
			<td class="required"> <?= _l("Post Code:"); ?></td>
			<td><input type="text" name="postcode" value="<?= $postcode; ?>"/></td>
		</tr>
	</table>
	<input type="button" value="<?= _l("Get Quotes"); ?>" id="button-quote" class="button"/>
</form>

<div id="shipping_quote_template" class="shipping_quote" style="display:none">
	<h2><?= _l("Please select the preferred shipping method to use on this order."); ?></h2>

	<form action="<?= $apply; ?>" method="post" enctype="multipart/form-data">
		<table class="quote_method radio">
			<tr id="%code%" class="code">
				<td colspan="3"><b>%code_title%</b></td>
			</tr>
			<tr class="method highlight" onclick="$(this).find('[name=shipping_method]').attr('checked','checked')">
				<td><input type="radio" name="shipping_method" value="%method_id%" id="%method%"/></td>
				<td><label for="%method%">%title%</label></td>
				<td class="method"><label for="%method%">%text%</label></td>
			</tr>
			<tr class="error_msg">
				<td colspan="3">
					<div class="error">%error%</div>
				</td>
			</tr>
		</table>
		<div class="form" id="quote_full_address" style="display:none">
			<input type="hidden" name="add_address" value="0"/>
			<?= $form_address; ?>
			<input type="hidden" name="country_id" value=""/>
			<input type="hidden" name="zone_id" value=""/>
			<input type="hidden" name="postcode" value=""/>

			<div class="quote_address"></div>
		</div>
		<input type="hidden" name="redirect" value="<?= $redirect; ?>"/>
		<input type="submit" onclick="return apply_shipping_quote($(this));" value="<?= _l("Apply Shipping"); ?>"
		       class="button"/>
	</form>
</div>

<?= $this->builder->js('load_zones', '#cart_shipping', '.country_select', '.zone_select'); ?>

<script type="text/javascript">
	var code_template = $('#shipping_quote_template .code')[0].outerHTML;
	var method_template = $('#shipping_quote_template .method')[0].outerHTML;
	var error_template = $('#shipping_quote_template .error_msg')[0].outerHTML;
	$('#shipping_quote_template').find('.code, .method, .error_msg').remove();

	var shipping_quote_template = $('#shipping_quote_template').clone().attr('id', '');
	$('#shipping_quote_template').remove();

	$('#button-quote').click(function () {
		var shipping_quote = shipping_quote_template.clone();

		country_id = $('#cart_shipping [name=country_id]').val();
		country_name = $('#cart_shipping [name=country_id] option[value=" + country_id + "]').html();
		zone_id = $('#cart_shipping [name=zone_id]').val();
		zone_name = $('#cart_shipping [name=zone_id] option[value=" + zone_id + "]').html();
		postcode = $('#cart_shipping [name=postcode]').val();

		shipping_quote.find('[name=country_id]').val(country_id);
		shipping_quote.find('.country_id').html(country_name);
		shipping_quote.find('[name=zone_id]').val(zone_id);
		shipping_quote.find('.zone_id').html(zone_name);
		shipping_quote.find('[name=postcode]').val(postcode);
		shipping_quote.find('.postcode').html(postcode);
		shipping_quote.find('.quote_address').html(zone_name + ', ' + postcode + ', ' + country_name);

		$.ajax({
			url: "<?= $url_quote; ?>",
			type: 'post',
			data: $('#cart_shipping').serialize(),
			dataType: 'json',
			beforeSend: function () {
				$('#button-quote').attr('disabled', true);
				$('#button-quote').after('<span class="wait"><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /></span>');
			},
			complete: function () {
				$('#button-quote').attr('disabled', false);
				$('.wait').remove();
			},
			success: function (json) {
				$('.message_box, .error').remove();

				if (json['error']) {
					msg = '';
					for (var e in json['error']) {
						msg += (msg ? "<br />" : '') + json['error'][e];
						$('#cart_shipping [name=" + e + "]').after('<span class="error">' + json['error'][e] + '</span>');
					}
					show_msg('warning', msg);
				}
				else if (json['shipping_method']) {
					for (method_id in json['shipping_method']) {
						code = json['shipping_method'][method_id]['code'];
						code_title = json['shipping_method'][method_id]['code_title'];

						if (!shipping_quote.find('#' + code).length) {
							code_t = code_template
								.replace(/%code_title%/g, code_title)
								.replace(/%code%/g, code);

							shipping_quote.find('table.quote_method').append(code_t);
						}

						if (!json['shipping_method'][method_id]['error']) {
							title = json['shipping_method'][method_id]['title'];
							method = json['shipping_method'][method_id]['method'];
							text = json['shipping_method'][method_id]['text'];

							method_t = method_template
								.replace(/%title%/g, title)
								.replace(/%method%/g, method)
								.replace(/%code%/g, code)
								.replace(/%method_id%/, method_id)
								.replace(/%text%/g, text);

							shipping_quote.find('#' + code).after(method_t);

							if (method == '<?= $shipping_method; ?>') {
								shipping_quote.find('#' + method).attr('checked', 'checked');
							}
						} else {
							error = json['shipping_method'][method_id]['error'];
							shipping_quote.find('table.quote_method').append(error_template.replace(/%error%/g, error));
						}
					}

					colorbox(shipping_quote);
				}
			}
		});
	});

	function apply_shipping_quote(context) {
		form = context.closest('form');

		form.find('input[name=redirect]').remove();

		$.ajax({
			url: form.attr('action'),
			type: 'post',
			data: form.serialize(),
			dataType: 'json',
			beforeSend: function () {
				context.attr('disabled', true);
				context.after('<span class="wait"><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /></span>');
			},
			complete: function () {
				context.attr('disabled', false);
				$('.wait').remove();
			},

			success: function (json) {
				if (json['request_address']) {
					form.find('[name=add_address]').val(1);
					form.find('#quote_full_address').slideDown();
					show_msg('notify', '<?= _l("Please provide additional shipping information."); ?>');
					return;
				}

				show_msgs(json);

				if (json['success']) {
					$.colorbox.close();
					load_block($('#cart_block_total'), 'block/cart/total');
				}
			}
		});

		return false;
	}
</script>
