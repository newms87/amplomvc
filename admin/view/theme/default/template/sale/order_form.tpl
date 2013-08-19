<?= $header; ?>
	<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
	<div class="heading">
		<h1><img src="<?= HTTP_THEME_IMAGE . 'order.png'; ?>" alt=""/> <?= $head_title; ?></h1>

		<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a
				href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
	</div>
	<div class="content">
	<div id="vtabs" class="vtabs"><a href="#tab-customer"><?= $tab_customer; ?></a><a
			href="#tab-payment"><?= $tab_payment; ?></a><a href="#tab-shipping"><?= $tab_shipping; ?></a><a
			href="#tab-product"><?= $tab_product; ?></a><a href="#tab-voucher"><?= $tab_voucher; ?></a><a
			href="#tab-total"><?= $tab_total; ?></a></div>
	<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
	<div id="tab-customer" class="vtabs-content">
		<table class="form">
			<tr>
				<td class="left"><?= $entry_store; ?></td>
				<td class="left">
					<? $this->builder->set_config('store_id', 'name'); ?>
					<?= $this->builder->build('select', $data_stores, "store_id", $store_id); ?>
				</td>
			</tr>
			<tr>
				<td><?= $entry_customer; ?></td>
				<td><input type="text" name="customer" value="<?= $customer; ?>"/>
					<input type="hidden" name="customer_id" value="<?= $customer_id; ?>"/>
					<input type="hidden" name="customer_group_id" value="<?= $customer_group_id; ?>"/></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_firstname; ?></td>
				<td><input type="text" name="firstname" value="<?= $firstname; ?>"/></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_lastname; ?></td>
				<td><input type="text" name="lastname" value="<?= $lastname; ?>"/></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_email; ?></td>
				<td><input type="text" name="email" value="<?= $email; ?>"/></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_telephone; ?></td>
				<td><input type="text" name="telephone" value="<?= $telephone; ?>"/></td>
			</tr>
			<tr>
				<td><?= $entry_fax; ?></td>
				<td><input type="text" name="fax" value="<?= $fax; ?>"/></td>
			</tr>
		</table>
	</div>
	<div id="tab-payment" class="vtabs-content">
		<table class="form">
			<tr>
				<td><?= $entry_address; ?></td>
				<td><select name="payment_address">
						<option value="0" selected="selected"><?= $text_none; ?></option>
						<? foreach ($addresses as $address) { ?>
							<option
								value="<?= $address['address_id']; ?>"><?= $address['firstname'] . ' ' . $address['lastname'] . ', ' . $address['address_1'] . ', ' . $address['city'] . ', ' . $address['country']; ?></option>
						<? } ?>
					</select></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_firstname; ?></td>
				<td><input type="text" name="payment_firstname" value="<?= $payment_firstname; ?>"/></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_lastname; ?></td>
				<td><input type="text" name="payment_lastname" value="<?= $payment_lastname; ?>"/></td>
			</tr>
			<tr>
				<td><?= $entry_company; ?></td>
				<td><input type="text" name="payment_company" value="<?= $payment_company; ?>"/></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_address_1; ?></td>
				<td><input type="text" name="payment_address_1" value="<?= $payment_address_1; ?>"/></td>
			</tr>
			<tr>
				<td><?= $entry_address_2; ?></td>
				<td><input type="text" name="payment_address_2" value="<?= $payment_address_2; ?>"/></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_city; ?></td>
				<td><input type="text" name="payment_city" value="<?= $payment_city; ?>"/></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_postcode; ?></td>
				<td><input type="text" name="payment_postcode" value="<?= $payment_postcode; ?>"/></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_country; ?></td>
				<td>
					<?= $this->builder->set_config('country_id', 'name'); ?>
					<?= $this->builder->build('select', $countries, "payment_country_id", $payment_country_id, array('class' => "country_select")); ?>
				</td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_zone; ?></td>
				<td><select name="payment_zone_id" class="zone_select" zone_id="<?= $payment_zone_id; ?>"></select></td>
			</tr>
		</table>
	</div>
	<div id="tab-shipping" class="vtabs-content">
		<table class="form">
			<tr>
				<td><?= $entry_address; ?></td>
				<td><select name="shipping_address">
						<option value="0" selected="selected"><?= $text_none; ?></option>
						<? foreach ($addresses as $address) { ?>
							<option
								value="<?= $address['address_id']; ?>"><?= $address['firstname'] . ' ' . $address['lastname'] . ', ' . $address['address_1'] . ', ' . $address['city'] . ', ' . $address['country']; ?></option>
						<? } ?>
					</select></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_firstname; ?></td>
				<td><input type="text" name="shipping_firstname" value="<?= $shipping_firstname; ?>"/></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_lastname; ?></td>
				<td><input type="text" name="shipping_lastname" value="<?= $shipping_lastname; ?>"/></td>
			</tr>
			<tr>
				<td><?= $entry_company; ?></td>
				<td><input type="text" name="shipping_company" value="<?= $shipping_company; ?>"/></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_address_1; ?></td>
				<td><input type="text" name="shipping_address_1" value="<?= $shipping_address_1; ?>"/></td>
			</tr>
			<tr>
				<td><?= $entry_address_2; ?></td>
				<td><input type="text" name="shipping_address_2" value="<?= $shipping_address_2; ?>"/></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_city; ?></td>
				<td><input type="text" name="shipping_city" value="<?= $shipping_city; ?>"/></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_postcode; ?></td>
				<td><input type="text" name="shipping_postcode" value="<?= $shipping_postcode; ?>"/></td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_country; ?></td>
				<td>
					<?= $this->builder->set_config('country_id', 'name'); ?>
					<?= $this->builder->build('select', $countries, "shipping_country_id", $shipping_country_id, array('class' => "country_select")); ?>
				</td>
			</tr>
			<tr>
				<td class="required"> <?= $entry_zone; ?></td>
				<td><select name="shipping_zone_id" zone_id="<?= $shipping_zone_id; ?>" class="zone_select"></select></td>
			</tr>
		</table>
	</div>
	<div id="tab-product" class="vtabs-content">
		<table class="list">
			<thead>
			<tr>
				<td></td>
				<td class="left"><?= $column_product; ?></td>
				<td class="left"><?= $column_model; ?></td>
				<td class="right"><?= $column_quantity; ?></td>
				<td class="right"><?= $column_price; ?></td>
				<td class="right"><?= $column_total; ?></td>
			</tr>
			</thead>
			<? $product_row = 0; ?>
			<? $option_row = 0; ?>
			<? $download_row = 0; ?>
			<tbody id="product">
			<? if ($order_products) { ?>
				<? foreach ($order_products as $order_product) { ?>
					<tr id="product-row<?= $product_row; ?>">
						<td class="center" style="width: 3px;"><img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>"
						                                            title="<?= $button_remove; ?>"
						                                            alt="<?= $button_remove; ?>" style="cursor: pointer;"
						                                            onclick="$('#product-row<?= $product_row; ?>').remove(); $('#button-update').trigger('click');"/>
						</td>
						<td class="left"><?= $order_product['name']; ?><br/>
							<input type="hidden" name="order_product[<?= $product_row; ?>][order_product_id]"
							       value="<?= $order_product['order_product_id']; ?>"/>
							<input type="hidden" name="order_product[<?= $product_row; ?>][product_id]"
							       value="<?= $order_product['product_id']; ?>"/>
							<input type="hidden" name="order_product[<?= $product_row; ?>][name]"
							       value="<?= $order_product['name']; ?>"/>
							<? foreach ($order_product['option'] as $option) { ?>
								-
								<small><?= $option['name']; ?>: <?= $option['value']; ?></small><br/>
								<input type="hidden"
								       name="order_product[<?= $product_row; ?>][order_option][<?= $option_row; ?>][order_option_id]"
								       value="<?= $option['order_option_id']; ?>"/>
								<input type="hidden"
								       name="order_product[<?= $product_row; ?>][order_option][<?= $option_row; ?>][product_option_id]"
								       value="<?= $option['product_option_id']; ?>"/>
								<input type="hidden"
								       name="order_product[<?= $product_row; ?>][order_option][<?= $option_row; ?>][product_option_value_id]"
								       value="<?= $option['product_option_value_id']; ?>"/>
								<input type="hidden"
								       name="order_product[<?= $product_row; ?>][order_option][<?= $option_row; ?>][name]"
								       value="<?= $option['name']; ?>"/>
								<input type="hidden"
								       name="order_product[<?= $product_row; ?>][order_option][<?= $option_row; ?>][value]"
								       value="<?= $option['value']; ?>"/>
								<input type="hidden"
								       name="order_product[<?= $product_row; ?>][order_option][<?= $option_row; ?>][type]"
								       value="<?= $option['type']; ?>"/>
								<? $option_row++; ?>
							<? } ?>
							<? foreach ($order_product['download'] as $download) { ?>
								<input type="hidden"
								       name="order_product[<?= $product_row; ?>][order_download][<?= $download_row; ?>][order_download_id]"
								       value="<?= $download['order_download_id']; ?>"/>
								<input type="hidden"
								       name="order_product[<?= $product_row; ?>][order_download][<?= $download_row; ?>][name]"
								       value="<?= $download['name']; ?>"/>
								<input type="hidden"
								       name="order_product[<?= $product_row; ?>][order_download][<?= $download_row; ?>][filename]"
								       value="<?= $download['filename']; ?>"/>
								<input type="hidden"
								       name="order_product[<?= $product_row; ?>][order_download][<?= $download_row; ?>][mask]"
								       value="<?= $download['mask']; ?>"/>
								<input type="hidden"
								       name="order_product[<?= $product_row; ?>][order_download][<?= $download_row; ?>][remaining]"
								       value="<?= $download['remaining']; ?>"/>
								<? $download_row++; ?>
							<? } ?></td>
						<td class="left"><?= $order_product['model']; ?>
							<input type="hidden" name="order_product[<?= $product_row; ?>][model]"
							       value="<?= $order_product['model']; ?>"/></td>
						<td class="right"><?= $order_product['quantity']; ?>
							<input type="hidden" name="order_product[<?= $product_row; ?>][quantity]"
							       value="<?= $order_product['quantity']; ?>"/></td>
						<td class="right"><?= $order_product['price']; ?>
							<input type="hidden" name="order_product[<?= $product_row; ?>][price]"
							       value="<?= $order_product['price']; ?>"/></td>
						<td class="right"><?= $order_product['total']; ?>
							<input type="hidden" name="order_product[<?= $product_row; ?>][total]"
							       value="<?= $order_product['total']; ?>"/>
							<input type="hidden" name="order_product[<?= $product_row; ?>][tax]"
							       value="<?= $order_product['tax']; ?>"/>
							<input type="hidden" name="order_product[<?= $product_row; ?>][reward]"
							       value="<?= $order_product['reward']; ?>"/></td>
					</tr>
					<? $product_row++; ?>
				<? } ?>
			<? } else { ?>
				<tr>
					<td class="center" colspan="6"><?= $text_no_results; ?></td>
				</tr>
			<? } ?>
			</tbody>
		</table>
		<table class="list">
			<thead>
			<tr>
				<td colspan="2" class="left"><?= $text_product; ?></td>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td class="left"><?= $entry_product; ?></td>
				<td class="left"><input type="text" name="product" value=""/>
					<input type="hidden" name="product_id" value=""/></td>
			</tr>
			<tr id="option"></tr>
			<tr>
				<td class="left"><?= $entry_quantity; ?></td>
				<td class="left"><input type="text" name="quantity" value="1"/></td>
			</tr>
			</tbody>
			<tfoot>
			<tr>
				<td class="left">&nbsp;</td>
				<td class="left"><a id="button-product" class="button"><?= $button_add_product; ?></a></td>
			</tr>
			</tfoot>
		</table>
	</div>
	<div id="tab-voucher" class="vtabs-content">
		<table class="list">
			<thead>
			<tr>
				<td></td>
				<td class="left"><?= $column_product; ?></td>
				<td class="left"><?= $column_model; ?></td>
				<td class="right"><?= $column_quantity; ?></td>
				<td class="right"><?= $column_price; ?></td>
				<td class="right"><?= $column_total; ?></td>
			</tr>
			</thead>
			<tbody id="voucher">
			<? $voucher_row = 0; ?>
			<? if ($order_vouchers) { ?>
				<? foreach ($order_vouchers as $order_voucher) { ?>
					<tr id="voucher-row<?= $voucher_row; ?>">
						<td class="center" style="width: 3px;"><img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>"
						                                            title="<?= $button_remove; ?>"
						                                            alt="<?= $button_remove; ?>" style="cursor: pointer;"
						                                            onclick="$('#voucher-row<?= $voucher_row; ?>').remove(); $('#button-update').trigger('click');"/>
						</td>
						<td class="left"><?= $order_voucher['description']; ?>
							<input type="hidden" name="order_voucher[<?= $voucher_row; ?>][order_voucher_id]"
							       value="<?= $order_voucher['order_voucher_id']; ?>"/>
							<input type="hidden" name="order_voucher[<?= $voucher_row; ?>][voucher_id]"
							       value="<?= $order_voucher['voucher_id']; ?>"/>
							<input type="hidden" name="order_voucher[<?= $voucher_row; ?>][description]"
							       value="<?= $order_voucher['description']; ?>"/>
							<input type="hidden" name="order_voucher[<?= $voucher_row; ?>][code]"
							       value="<?= $order_voucher['code']; ?>"/>
							<input type="hidden" name="order_voucher[<?= $voucher_row; ?>][from_name]"
							       value="<?= $order_voucher['from_name']; ?>"/>
							<input type="hidden" name="order_voucher[<?= $voucher_row; ?>][from_email]"
							       value="<?= $order_voucher['from_email']; ?>"/>
							<input type="hidden" name="order_voucher[<?= $voucher_row; ?>][to_name]"
							       value="<?= $order_voucher['to_name']; ?>"/>
							<input type="hidden" name="order_voucher[<?= $voucher_row; ?>][to_email]"
							       value="<?= $order_voucher['to_email']; ?>"/>
							<input type="hidden" name="order_voucher[<?= $voucher_row; ?>][voucher_theme_id]"
							       value="<?= $order_voucher['voucher_theme_id']; ?>"/>
							<input type="hidden" name="order_voucher[<?= $voucher_row; ?>][message]"
							       value="<?= $order_voucher['message']; ?>"/>
							<input type="hidden" name="order_voucher[<?= $voucher_row; ?>][amount]"
							       value="<?= $order_voucher['amount']; ?>"/></td>
						<td class="left"></td>
						<td class="right">1</td>
						<td class="right"><?= $order_voucher['amount']; ?></td>
						<td class="right"><?= $order_voucher['amount']; ?></td>
					</tr>
					<? $voucher_row++; ?>
				<? } ?>
			<? } else { ?>
				<tr>
					<td class="center" colspan="6"><?= $text_no_results; ?></td>
				</tr>
			<? } ?>
			</tbody>
		</table>
		<table class="list">
			<thead>
			<tr>
				<td colspan="2" class="left"><?= $text_voucher; ?></td>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td class="left"><span class="required"></span> <?= $entry_to_name; ?></td>
				<td class="left"><input type="text" name="to_name" value=""/></td>
			</tr>
			<tr>
				<td class="left"><span class="required"></span> <?= $entry_to_email; ?></td>
				<td class="left"><input type="text" name="to_email" value=""/></td>
			</tr>
			<tr>
				<td class="left"><span class="required"></span> <?= $entry_from_name; ?></td>
				<td class="left"><input type="text" name="from_name" value=""/></td>
			</tr>
			<tr>
				<td class="left"><span class="required"></span> <?= $entry_from_email; ?></td>
				<td class="left"><input type="text" name="from_email" value=""/></td>
			</tr>
			<tr>
				<td class="left"><span class="required"></span> <?= $entry_theme; ?></td>
				<td class="left"><select name="voucher_theme_id">
						<? foreach ($voucher_themes as $voucher_theme) { ?>
							<option
								value="<?= $voucher_theme['voucher_theme_id']; ?>"><?= addslashes($voucher_theme['name']); ?></option>
						<? } ?>
					</select></td>
			</tr>
			<tr>
				<td class="left"><?= $entry_message; ?></td>
				<td class="left"><textarea name="message" cols="40" rows="5"></textarea></td>
			</tr>
			<tr>
				<td class="left"><span class="required"></span> <?= $entry_amount; ?></td>
				<td class="left"><input type="text" name="amount" value="25.00" size="5"/></td>
			</tr>
			</tbody>
			<tfoot>
			<tr>
				<td class="left">&nbsp;</td>
				<td class="left"><a id="button-voucher" class="button"><?= $button_add_voucher; ?></a></td>
			</tr>
			</tfoot>
		</table>
	</div>
	<div id="tab-total" class="vtabs-content">
		<table class="list">
			<thead>
			<tr>
				<td class="left"><?= $column_product; ?></td>
				<td class="left"><?= $column_model; ?></td>
				<td class="right"><?= $column_quantity; ?></td>
				<td class="right"><?= $column_price; ?></td>
				<td class="right"><?= $column_total; ?></td>
			</tr>
			</thead>
			<tbody id="total">
			<? $total_row = 0; ?>
			<? if ($order_products || $order_vouchers || $order_totals) { ?>
				<? foreach ($order_products as $order_product) { ?>
					<tr>
						<td class="left"><?= $order_product['name']; ?><br/>
							<? foreach ($order_product['option'] as $option) { ?>
								-
								<small><?= $option['name']; ?>: <?= $option['value']; ?></small><br/>
							<? } ?></td>
						<td class="left"><?= $order_product['model']; ?></td>
						<td class="right"><?= $order_product['quantity']; ?></td>
						<td class="right"><?= $order_product['price']; ?></td>
						<td class="right"><?= $order_product['total']; ?></td>
					</tr>
				<? } ?>
				<? foreach ($order_vouchers as $order_voucher) { ?>
					<tr>
						<td class="left"><?= $order_voucher['description']; ?></td>
						<td class="left"></td>
						<td class="right">1</td>
						<td class="right"><?= $order_voucher['amount']; ?></td>
						<td class="right"><?= $order_voucher['amount']; ?></td>
					</tr>
				<? } ?>
				<? foreach ($order_totals as $order_total) { ?>
					<tr id="total-row<?= $total_row; ?>">
						<td class="right" colspan="4"><?= $order_total['title']; ?>:
							<input type="hidden" name="order_total[<?= $total_row; ?>][order_total_id]"
							       value="<?= $order_total['order_total_id']; ?>"/>
							<input type="hidden" name="order_total[<?= $total_row; ?>][code]"
							       value="<?= $order_total['code']; ?>"/>
							<input type="hidden" name="order_total[<?= $total_row; ?>][title]"
							       value="<?= $order_total['title']; ?>"/>
							<input type="hidden" name="order_total[<?= $total_row; ?>][text]"
							       value="<?= $order_total['text']; ?>"/>
							<input type="hidden" name="order_total[<?= $total_row; ?>][value]"
							       value="<?= $order_total['value']; ?>"/>
							<input type="hidden" name="order_total[<?= $total_row; ?>][sort_order]"
							       value="<?= $order_total['sort_order']; ?>"/></td>
						<td class="right"><?= $order_total['value']; ?></td>
					</tr>
					<? $total_row++; ?>
				<? } ?>
			<? } else { ?>
				<tr>
					<td class="center" colspan="5"><?= $text_no_results; ?></td>
				</tr>
			<? } ?>
			</tbody>
		</table>
		<table class="list">
			<thead>
			<tr>
				<td class="left" colspan="2"><?= $text_order; ?></td>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td class="left"><?= $entry_shipping; ?></td>
				<td class="left"><select name="shipping">
						<option value=""><?= $text_select; ?></option>
						<? if ($shipping_code) { ?>
							<option value="<?= $shipping_code; ?>" selected="selected"><?= $shipping_method; ?></option>
						<? } ?>
					</select>
					<input type="hidden" name="shipping_method" value="<?= $shipping_method; ?>"/>
					<input type="hidden" name="shipping_code" value="<?= $shipping_code; ?>"/></td>
			</tr>
			<tr>
				<td class="left"><?= $entry_payment; ?></td>
				<td class="left"><select name="payment">
						<option value=""><?= $text_select; ?></option>
						<? if ($payment_code) { ?>
							<option value="<?= $payment_code; ?>" selected="selected"><?= $payment_method; ?></option>
						<? } ?>
					</select>
					<input type="hidden" name="payment_method" value="<?= $payment_method; ?>"/>
					<input type="hidden" name="payment_code" value="<?= $payment_code; ?>"/></td>
			</tr>
			<tr>
				<td class="left"><?= $entry_coupon; ?></td>
				<td class="left"><input type="text" name="coupon" value=""/></td>
			</tr>
			<tr>
				<td class="left"><?= $entry_voucher; ?></td>
				<td class="left"><input type="text" name="voucher" value=""/></td>
			</tr>
			<tr>
				<td class="left"><?= $entry_reward; ?></td>
				<td class="left"><input type="text" name="reward" value=""/></td>
			</tr>
			<tr>
				<td class="left"><?= $entry_order_status; ?></td>
				<td class="left"><select name="order_status_id">
						<? foreach ($order_statuses as $order_status) { ?>
							<? if ($order_status['order_status_id'] == $order_status_id) { ?>
								<option value="<?= $order_status['order_status_id']; ?>"
								        selected="selected"><?= $order_status['name']; ?></option>
							<? } else { ?>
								<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
							<? } ?>
						<? } ?>
					</select></td>
			</tr>
			<tr>
				<td class="left"><?= $entry_comment; ?></td>
				<td class="left"><textarea name="comment" cols="40" rows="5"><?= $comment; ?></textarea></td>
			</tr>
			<tr>
				<td class="left"><?= $entry_affiliate; ?></td>
				<td class="left"><input type="text" name="affiliate" value="<?= $affiliate; ?>"/>
					<input type="hidden" name="affiliate_id" value="<?= $affiliate_id; ?>"/></td>
			</tr>
			</tbody>
			<tfoot>
			<tr>
				<td class="left">&nbsp;</td>
				<td class="left"><a id="button-update" class="button"><?= $button_update_total; ?></a></td>
			</tr>
			</tfoot>
		</table>
	</div>
	</form>
	</div>
	</div>
	</div>
	<script type="text/javascript"><!--
		$.widget('custom.catcomplete', $.ui.autocomplete, {
			_renderMenu: function (ul, items) {
				var self = this, currentCategory = '';

				$.each(items, function (index, item) {
					if (item['category'] != currentCategory) {
						ul.append('<li class="ui-autocomplete-category">' + item['category'] + '</li>');

						currentCategory = item['category'];
					}

					self._renderItem(ul, item);
				});
			}
		});

		$('input[name=\'customer\']').catcomplete({
			delay: 0,
			source: function (request, response) {
				$.ajax({
					url: "<?= //TODO: standardize .. $url_autocomplete; ?>" + '&filter_name=' + encodeURIComponent(request.term),
					dataType: 'json',
					success: function (json) {
						response($.map(json, function (item) {
							return {
								category: item['customer_group'],
								label: item['name'],
								value: item['customer_id'],
								customer_group_id: item['customer_group_id'],
								firstname: item['firstname'],
								lastname: item['lastname'],
								email: item['email'],
								telephone: item['telephone'],
								fax: item['fax'],
								address: item['address']
							}
						}));
					}
				});
			},
			select: function (event, ui) {
				$('input[name=\'customer\']').attr('value', ui.item['label']);
				$('input[name=\'customer_id\']').attr('value', ui.item['value']);
				$('input[name=\'customer_group_id\']').attr('value', ui.item['customer_group_id']);
				$('input[name=\'firstname\']').attr('value', ui.item['firstname']);
				$('input[name=\'lastname\']').attr('value', ui.item['lastname']);
				$('input[name=\'email\']').attr('value', ui.item['email']);
				$('input[name=\'telephone\']').attr('value', ui.item['telephone']);
				$('input[name=\'fax\']').attr('value', ui.item['fax']);

				html = '<option value="0"><?= $text_none; ?></option>';

				for (i = 0; i < ui.item['address'].length; i++) {
					html += '<option value="' + ui.item['address'][i]['address_id'] + '">' + ui.item['address'][i]['firstname'] + ' ' + ui.item['address'][i]['lastname'] + ', ' + ui.item['address'][i]['address_1'] + ', ' + ui.item['address'][i]['city'] + ', ' + ui.item['address'][i]['country'] + '</option>';
				}

				$('select[name=\'shipping_address\']').html(html);
				$('select[name=\'payment_address\']').html(html);

				return false;
			}
		});

		$('input[name=\'affiliate\']').autocomplete({
			delay: 0,
			source: function (request, response) {
				$.ajax({
					url: "<?= HTTP_ADMIN . "index.php?route=sale/affiliate/autocomplete"; ?>" + '&filter_name=' + encodeURIComponent(request.term),
					dataType: 'json',
					success: function (json) {
						response($.map(json, function (item) {
							return {
								label: item['name'],
								value: item['affiliate_id'],
							}
						}));
					}
				});
			},
			select: function (event, ui) {
				$('input[name=\'affiliate\']').attr('value', ui.item['label']);
				$('input[name=\'affiliate_id\']').attr('value', ui.item['value']);

				return false;
			}
		});

		$('select[name=\'payment_address\']').bind('change', function () {
			$.ajax({
				url: "<?= HTTP_ADMIN . "index.php?route=sale/customer/address"; ?>" + '&address_id=' + this.value,
				dataType: 'json',
				success: function (json) {
					if (json != '') {
						$('input[name=\'payment_firstname\']').attr('value', json['firstname']);
						$('input[name=\'payment_lastname\']').attr('value', json['lastname']);
						$('input[name=\'payment_company\']').attr('value', json['company']);
						$('input[name=\'payment_address_1\']').attr('value', json['address_1']);
						$('input[name=\'payment_address_2\']').attr('value', json['address_2']);
						$('input[name=\'payment_city\']').attr('value', json['city']);
						$('input[name=\'payment_postcode\']').attr('value', json['postcode']);
						$('select[name=\'payment_country_id\']').attr('value', json['country_id']);
						$('select[name=\'payment_zone_id\']').load("<?= HTTP_ADMIN . "index.php?route=tool/data/load_zones"; ?>" + '&country_id=' + json['country_id'] + '&zone_id=' + json['zone_id']);
					}
				}
			});
		});

		$('select[name=\'shipping_address\']').bind('change', function () {
			$.ajax({
				url: "<?= HTTP_ADMIN . "index.php?route=sale/customer/address"; ?>" + '&address_id=' + this.value,
				dataType: 'json',
				success: function (json) {
					if (json != '') {
						$('input[name=\'shipping_firstname\']').attr('value', json['firstname']);
						$('input[name=\'shipping_lastname\']').attr('value', json['lastname']);
						$('input[name=\'shipping_company\']').attr('value', json['company']);
						$('input[name=\'shipping_address_1\']').attr('value', json['address_1']);
						$('input[name=\'shipping_address_2\']').attr('value', json['address_2']);
						$('input[name=\'shipping_city\']').attr('value', json['city']);
						$('input[name=\'shipping_postcode\']').attr('value', json['postcode']);
						$('select[name=\'shipping_country_id\']').attr('value', json['country_id']);
						$('select[name=\'shipping_zone_id\']').load("<?= HTTP_ADMIN . "index.php?route=tool/data/load_zones"; ?>" + '&country_id=' + json['country_id'] + '&zone_id=' + json['zone_id']);
					}
				}
			});
		});
//--></script>

<?= $this->builder->js('load_zones', 'table.form', '.country_select', '.zone_select'); ?>

	<script type="text/javascript"><!--
	$('input[name=\'product\']').autocomplete({
		delay: 0,
		source: function (request, response) {
			$.ajax({
				url: "<?= HTTP_ADMIN . "index.php?route=catalog/product/autocomplete"; ?>" + '&filter_name=' + encodeURIComponent(request.term),
				dataType: 'json',
				success: function (json) {
					response($.map(json, function (item) {
						return {
							label: item.name,
							value: item.product_id,
							model: item.model,
							option: item.option,
							price: item.price
						}
					}));
				}
			});
		},
		select: function (event, ui) {
			$('input[name=\'product\']').attr('value', ui.item['label']);
			$('input[name=\'product_id\']').attr('value', ui.item['value']);

			if (ui.item['option'] != '') {
				html = '';

				for (i = 0; i < ui.item['option'].length; i++) {
					option = ui.item['option'][i];

					if (option['type'] == 'select') {
						html += '<div id="option-' + option['product_option_id'] + '">';

						if (option['required']) {
							html += '<span class="required"></span> ';
						}

						html += option['name'] + '<br />';
						html += '<select name="option[' + option['product_option_id'] + ']">';
						html += '<option value=""><?= $text_select; ?></option>';

						for (j = 0; j < option['option_value'].length; j++) {
							option_value = option['option_value'][j];

							html += '<option value="' + option_value['product_option_value_id'] + '">' + option_value['name'];

							if (option_value['price']) {
								html += ' (' + option_value['price'] + ')';
							}

							html += '</option>';
						}

						html += '</select>';
						html += '</div>';
						html += '<br />';
					}

					if (option['type'] == 'radio') {
						html += '<div id="option-' + option['product_option_id'] + '">';

						if (option['required']) {
							html += '<span class="required"></span> ';
						}

						html += option['name'] + '<br />';
						html += '<select name="option[' + option['product_option_id'] + ']">';
						html += '<option value=""><?= $text_select; ?></option>';

						for (j = 0; j < option['option_value'].length; j++) {
							option_value = option['option_value'][j];

							html += '<option value="' + option_value['product_option_value_id'] + '">' + option_value['name'];

							if (option_value['price']) {
								html += ' (' + option_value['price'] + ')';
							}

							html += '</option>';
						}

						html += '</select>';
						html += '</div>';
						html += '<br />';
					}

					if (option['type'] == 'checkbox') {
						html += '<div id="option-' + option['product_option_id'] + '">';

						if (option['required']) {
							html += '<span class="required"></span> ';
						}

						html += option['name'] + '<br />';

						for (j = 0; j < option['option_value'].length; j++) {
							option_value = option['option_value'][j];

							html += '<input type="checkbox" name="option[' + option['product_option_id'] + '][]" value="' + option_value['product_option_value_id'] + '" id="option-value-' + option_value['product_option_value_id'] + '" />';
							html += '<label for="option-value-' + option_value['product_option_value_id'] + '">' + option_value['name'];

							if (option_value['price']) {
								html += ' (' + option_value['price'] + ')';
							}

							html += '</label>';
							html += '<br />';
						}

						html += '</div>';
						html += '<br />';
					}

					if (option['type'] == 'image') {
						html += '<div id="option-' + option['product_option_id'] + '">';

						if (option['required']) {
							html += '<span class="required"></span> ';
						}

						html += option['name'] + '<br />';
						html += '<select name="option[' + option['product_option_id'] + ']">';
						html += '<option value=""><?= $text_select; ?></option>';

						for (j = 0; j < option['option_value'].length; j++) {
							option_value = option['option_value'][j];

							html += '<option value="' + option_value['product_option_value_id'] + '">' + option_value['name'];

							if (option_value['price']) {
								html += ' (' + option_value['price'] + ')';
							}

							html += '</option>';
						}

						html += '</select>';
						html += '</div>';
						html += '<br />';
					}

					if (option['type'] == 'text') {
						html += '<div id="option-' + option['product_option_id'] + '">';

						if (option['required']) {
							html += '<span class="required"></span> ';
						}

						html += option['name'] + '<br />';
						html += '<input type="text" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" />';
						html += '</div>';
						html += '<br />';
					}

					if (option['type'] == 'textarea') {
						html += '<div id="option-' + option['product_option_id'] + '">';

						if (option['required']) {
							html += '<span class="required"></span> ';
						}

						html += option['name'] + '<br />';
						html += '<textarea name="option[' + option['product_option_id'] + ']" cols="40" rows="5">' + option['option_value'] + '</textarea>';
						html += '</div>';
						html += '<br />';
					}

					if (option['type'] == 'file') {
						html += '<div id="option-' + option['product_option_id'] + '">';

						if (option['required']) {
							html += '<span class="required"></span> ';
						}

						html += option['name'] + '<br />';
						html += '<a id="button-option-' + option['product_option_id'] + '" class="button"><?= $button_upload; ?></a>';
						html += '<input type="hidden" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" />';
						html += '</div>';
						html += '<br />';
					}

					if (option['type'] == 'date') {
						html += '<div id="option-' + option['product_option_id'] + '">';

						if (option['required']) {
							html += '<span class="required"></span> ';
						}

						html += option['name'] + '<br />';
						html += '<input type="text" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" class="datepicker" />';
						html += '</div>';
						html += '<br />';
					}

					if (option['type'] == 'datetime') {
						html += '<div id="option-' + option['product_option_id'] + '">';

						if (option['required']) {
							html += '<span class="required"></span> ';
						}

						html += option['name'] + '<br />';
						html += '<input type="text" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" class="datetimepicker" />';
						html += '</div>';
						html += '<br />';
					}

					if (option['type'] == 'time') {
						html += '<div id="option-' + option['product_option_id'] + '">';

						if (option['required']) {
							html += '<span class="required"></span> ';
						}

						html += option['name'] + '<br />';
						html += '<input type="text" name="option[' + option['product_option_id'] + ']" value="' + option['option_value'] + '" class="time" />';
						html += '</div>';
						html += '<br />';
					}
				}

				$('#option').html('<td class="left"><?= $entry_option; ?></td><td class="left">' + html + '</td>');

				for (i = 0; i < ui.item.option.length; i++) {
					option = ui.item.option[i];

					if (option['type'] == 'file') {
						new AjaxUpload('#button-option-' + option['product_option_id'], {
							action: "<?= HTTP_ADMIN . "index.php?route=sale/order/upload"; ?>",
							name: 'file',
							autoSubmit: true,
							responseType: 'json',
							data: option,
							onSubmit: function (file, extension) {
								$('#button-option-' + (this._settings.data['product_option_id'] + '-' + this._settings.data['product_option_id'])).after('<img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" class="loading" />');
							},
							onComplete: function (file, json) {

								$('.error').remove();

								if (json['success']) {
									alert(json['success']);

									$('input[name=\'option[' + this._settings.data['product_option_id'] + ']\']').attr('value', json['file']);
								}

								if (json.error) {
									$('#option-' + this._settings.data['product_option_id']).after('<span class="error">' + json['error'] + '</span>');
								}

								$('.loading').remove();
							}
						});
					}
				}

				$('.date').datepicker({dateFormat: 'yy-mm-dd'});
				$('.datetime').datetimepicker({
					dateFormat: 'yy-mm-dd',
					timeFormat: 'h:m'
				});
				$('.time').timepicker({timeFormat: 'h:m'});
			} else {
				$('#option td').remove();
			}

			return false;
		}
	});
//--></script>
	<script type="text/javascript"><!--
		$('select[name=\'payment\']').bind('change', function () {
			if (this.value) {
				$('input[name=\'payment_method\']').attr('value', $('select[name=\'payment\'] option:selected').text());
			} else {
				$('input[name=\'payment_method\']').attr('value', '');
			}

			$('input[name=\'payment_code\']').attr('value', this.value);
		});

		$('select[name=\'shipping\']').bind('change', function () {
			if (this.value) {
				$('input[name=\'shipping_method\']').attr('value', $('select[name=\'shipping\'] option:selected').text());
			} else {
				$('input[name=\'shipping_method\']').attr('value', '');
			}

			$('input[name=\'shipping_code\']').attr('value', this.value);
		});
//--></script>
	<script type="text/javascript"><!--
	$('#button-product, #button-voucher, #button-update').live('click', function () {
		data = '#tab-customer input[type=\'text\'], #tab-customer input[type=\'hidden\'], #tab-customer input[type=\'radio\']:checked, #tab-customer input[type=\'checkbox\']:checked, #tab-customer select, #tab-customer textarea, ';
		data += '#tab-payment input[type=\'text\'], #tab-payment input[type=\'hidden\'], #tab-payment input[type=\'radio\']:checked, #tab-payment input[type=\'checkbox\']:checked, #tab-payment select, #tab-payment textarea, ';
		data += '#tab-shipping input[type=\'text\'], #tab-shipping input[type=\'hidden\'], #tab-shipping input[type=\'radio\']:checked, #tab-shipping input[type=\'checkbox\']:checked, #tab-shipping select, #tab-shipping textarea, ';

		if ($(this).attr('id') == 'button-product') {
			data += '#tab-product input[type=\'text\'], #tab-product input[type=\'hidden\'], #tab-product input[type=\'radio\']:checked, #tab-product input[type=\'checkbox\']:checked, #tab-product select, #tab-product textarea, ';
		} else {
			data += '#product input[type=\'text\'], #product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select, #product textarea, ';
		}

		if ($(this).attr('id') == 'button-voucher') {
			data += '#tab-voucher input[type=\'text\'], #tab-voucher input[type=\'hidden\'], #tab-voucher input[type=\'radio\']:checked, #tab-voucher input[type=\'checkbox\']:checked, #tab-voucher select, #tab-voucher textarea, ';
		} else {
			data += '#voucher input[type=\'text\'], #voucher input[type=\'hidden\'], #voucher input[type=\'radio\']:checked, #voucher input[type=\'checkbox\']:checked, #voucher select, #voucher textarea, ';
		}

		data += '#tab-total input[type=\'text\'], #tab-total input[type=\'hidden\'], #tab-total input[type=\'radio\']:checked, #tab-total input[type=\'checkbox\']:checked, #tab-total select, #tab-total textarea';

		$.ajax({
			url: '<?= $store_url; ?>index.php?route=checkout/manual',
			type: 'post',
			data: $(data),
			dataType: 'json',
			beforeSend: function () {
				$('.success, .warning, .attention, .error').remove();

				$('.box').before('<div class="attention"><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> <?= $text_wait; ?></div>');
			},
			success: function (json) {
				$('.success, .warning, .attention, .error').remove();

				// Check for errors
				if (json['error']) {
					if (json['error']['warning']) {
						$('.box').before('<div class="message_box warning">' + json['error']['warning'] + '</div>');
					}

					// Order Details
					if (json['error']['customer']) {
						$('.box').before('<span class="error">' + json['error']['customer'] + '</span>');
					}

					if (json['error']['firstname']) {
						$('input[name=\'firstname\']').after('<span class="error">' + json['error']['firstname'] + '</span>');
					}

					if (json['error']['lastname']) {
						$('input[name=\'lastname\']').after('<span class="error">' + json['error']['lastname'] + '</span>');
					}

					if (json['error']['email']) {
						$('input[name=\'email\']').after('<span class="error">' + json['error']['email'] + '</span>');
					}

					if (json['error']['telephone']) {
						$('input[name=\'telephone\']').after('<span class="error">' + json['error']['telephone'] + '</span>');
					}

					// Payment Address
					if (json['error']['payment']) {
						if (json['error']['payment']['firstname']) {
							$('input[name=\'payment_firstname\']').after('<span class="error">' + json['error']['payment']['firstname'] + '</span>');
						}

						if (json['error']['payment']['lastname']) {
							$('input[name=\'payment_lastname\']').after('<span class="error">' + json['error']['payment']['lastname'] + '</span>');
						}

						if (json['error']['payment']['address_1']) {
							$('input[name=\'payment_address_1\']').after('<span class="error">' + json['error']['payment']['address_1'] + '</span>');
						}

						if (json['error']['payment']['city']) {
							$('input[name=\'payment_city\']').after('<span class="error">' + json['error']['payment']['city'] + '</span>');
						}

						if (json['error']['payment']['country']) {
							$('select[name=\'payment_country_id\']').after('<span class="error">' + json['error']['payment']['country'] + '</span>');
						}

						if (json['error']['payment']['zone']) {
							$('select[name=\'payment_zone_id\']').after('<span class="error">' + json['error']['payment']['zone'] + '</span>');
						}

						if (json['error']['payment']['postcode']) {
							$('input[name=\'payment_postcode\']').after('<span class="error">' + json['error']['payment']['postcode'] + '</span>');
						}
					}

					// Shipping	Address
					if (json['error']['shipping']) {
						if (json['error']['shipping']['firstname']) {
							$('input[name=\'shipping_firstname\']').after('<span class="error">' + json['error']['shipping']['firstname'] + '</span>');
						}

						if (json['error']['shipping']['lastname']) {
							$('input[name=\'shipping_lastname\']').after('<span class="error">' + json['error']['shipping']['lastname'] + '</span>');
						}

						if (json['error']['shipping']['address_1']) {
							$('input[name=\'shipping_address_1\']').after('<span class="error">' + json['error']['shipping']['address_1'] + '</span>');
						}

						if (json['error']['shipping']['city']) {
							$('input[name=\'shipping_city\']').after('<span class="error">' + json['error']['shipping']['city'] + '</span>');
						}

						if (json['error']['shipping']['country']) {
							$('select[name=\'shipping_country_id\']').after('<span class="error">' + json['error']['shipping']['country'] + '</span>');
						}

						if (json['error']['shipping_zone']) {
							$('select[name=\'shipping_zone_id\']').after('<span class="error">' + json['error']['shipping']['zone'] + '</span>');
						}

						if (json['error']['shipping']['postcode']) {
							$('input[name=\'shipping_postcode\']').after('<span class="error">' + json['error']['shipping']['postcode'] + '</span>');
						}
					}

					// Products
					if (json['error']['product']) {
						if (json['error']['product']['option']) {
							for (i in json['error']['product']['option']) {
								$('#option-' + i).after('<span class="error">' + json['error']['product']['option'][i] + '</span>');
							}
						}

						if (json['error']['product']['stock']) {
							$('.box').before('<div class="message_box warning">' + json['error']['product']['stock'] + '</div>');
						}

						if (json['error']['product']['minimum']) {
							for (i in json['error']['product']['minimum']) {
								$('.box').before('<div class="message_box warning">' + json['error']['product']['minimum'][i] + '</div>');
							}
						}
					} else {
						$('input[name=\'product\']').attr('value', '');
						$('input[name=\'product_id\']').attr('value', '');
						$('#option td').remove();
						$('input[name=\'quantity\']').attr('value', '1');
					}

					// Voucher
					if (json['error']['vouchers']) {
						if (json['error']['vouchers']['from_name']) {
							$('input[name=\'from_name\']').after('<span class="error">' + json['error']['vouchers']['from_name'] + '</span>');
						}

						if (json['error']['vouchers']['from_email']) {
							$('input[name=\'from_email\']').after('<span class="error">' + json['error']['vouchers']['from_email'] + '</span>');
						}

						if (json['error']['vouchers']['to_name']) {
							$('input[name=\'to_name\']').after('<span class="error">' + json['error']['vouchers']['to_name'] + '</span>');
						}

						if (json['error']['vouchers']['to_email']) {
							$('input[name=\'to_email\']').after('<span class="error">' + json['error']['vouchers']['to_email'] + '</span>');
						}

						if (json['error']['vouchers']['amount']) {
							$('input[name=\'amount\']').after('<span class="error">' + json['error']['vouchers']['amount'] + '</span>');
						}
					} else {
						$('input[name=\'from_name\']').attr('value', '');
						$('input[name=\'from_email\']').attr('value', '');
						$('input[name=\'to_name\']').attr('value', '');
						$('input[name=\'to_email\']').attr('value', '');
						$('textarea[name=\'message\']').attr('value', '');
						$('input[name=\'amount\']').attr('value', '25.00');
					}

					// Shipping Method
					if (json['error']['shipping_method']) {
						$('.box').before('<div class="message_box warning">' + json['error']['shipping_method'] + '</div>');
					}

					// Payment Method
					if (json['error']['payment_method']) {
						$('.box').before('<div class="message_box warning">' + json['error']['payment_method'] + '</div>');
					}

					// Coupon
					if (json['error']['coupon']) {
						$('.box').before('<div class="message_box warning">' + json['error']['coupon'] + '</div>');
					}

					// Voucher
					if (json['error']['voucher']) {
						$('.box').before('<div class="message_box warning">' + json['error']['voucher'] + '</div>');
					}

					// Reward Points
					if (json['error']['reward']) {
						$('.box').before('<div class="message_box warning">' + json['error']['reward'] + '</div>');
					}
				} else {
					$('input[name=\'product\']').attr('value', '');
					$('input[name=\'product_id\']').attr('value', '');
					$('#option td').remove();
					$('input[name=\'quantity\']').attr('value', '1');

					$('input[name=\'from_name\']').attr('value', '');
					$('input[name=\'from_email\']').attr('value', '');
					$('input[name=\'to_name\']').attr('value', '');
					$('input[name=\'to_email\']').attr('value', '');
					$('textarea[name=\'message\']').attr('value', '');
					$('input[name=\'amount\']').attr('value', '25.00');
				}

				if (json['success']) {
					$('.box').before('<div class="message_box success" style="display: none;">' + json['success'] + '</div>');

					$('.success').fadeIn('slow');
				}

				if (json['order_product'] != '') {
					var product_row = 0;
					var option_row = 0;
					var download_row = 0;

					html = '';

					for (i = 0; i < json['order_product'].length; i++) {
						product = json['order_product'][i];

						html += '<tr id="product-row' + product_row + '">';
						html += '	<td class="center" style="width: 3px;"><img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" title="<?= $button_remove; ?>" alt="<?= $button_remove; ?>" style="cursor: pointer;" onclick="$(\'#product-row' + product_row + '\').remove(); $(\'#button-update\').trigger(\'click\');" /></td>';
						html += '	<td class="left">' + product['name'] + '<br /><input type="hidden" name="order_product[' + product_row + '][order_product_id]" value="" /><input type="hidden" name="order_product[' + product_row + '][product_id]" value="' + product['product_id'] + '" /><input type="hidden" name="order_product[' + product_row + '][name]" value="' + product['name'] + '" />';

						if (product['option']) {
							for (j = 0; j < product['option'].length; j++) {
								option = product['option'][j];

								html += '	- <small>' + option['name'] + ': ' + option['value'] + '</small><br />';
								html += '	<input type="hidden" name="order_product[' + product_row + '][order_option][' + option_row + '][order_option_id]" value="' + option['order_option_id'] + '" />';
								html += '	<input type="hidden" name="order_product[' + product_row + '][order_option][' + option_row + '][product_option_id]" value="' + option['product_option_id'] + '" />';
								html += '	<input type="hidden" name="order_product[' + product_row + '][order_option][' + option_row + '][product_option_value_id]" value="' + option['product_option_value_id'] + '" />';
								html += '	<input type="hidden" name="order_product[' + product_row + '][order_option][' + option_row + '][name]" value="' + option['name'] + '" />';
								html += '	<input type="hidden" name="order_product[' + product_row + '][order_option][' + option_row + '][value]" value="' + option['value'] + '" />';
								html += '	<input type="hidden" name="order_product[' + product_row + '][order_option][' + option_row + '][type]" value="' + option['type'] + '" />';

								option_row++;
							}
						}

						if (product['download']) {
							for (j = 0; j < product['download'].length; j++) {
								download = product['download'][j];

								html += '	<input type="hidden" name="order_product[' + product_row + '][order_download][' + download_row + '][order_download_id]" value="' + download['order_download_id'] + '" />';
								html += '	<input type="hidden" name="order_product[' + product_row + '][order_download][' + download_row + '][name]" value="' + download['name'] + '" />';
								html += '	<input type="hidden" name="order_product[' + product_row + '][order_download][' + download_row + '][filename]" value="' + download['filename'] + '" />';
								html += '	<input type="hidden" name="order_product[' + product_row + '][order_download][' + download_row + '][mask]" value="' + download['mask'] + '" />';
								html += '	<input type="hidden" name="order_product[' + product_row + '][order_download][' + download_row + '][remaining]" value="' + download['remaining'] + '" />';

								download_row++;
							}
						}

						html += '	</td>';
						html += '	<td class="left">' + product['model'] + '<input type="hidden" name="order_product[' + product_row + '][model]" value="' + product['model'] + '" /></td>';
						html += '	<td class="right">' + product['quantity'] + '<input type="hidden" name="order_product[' + product_row + '][quantity]" value="' + product['quantity'] + '" /></td>';
						html += '	<td class="right">' + product['price'] + '<input type="hidden" name="order_product[' + product_row + '][price]" value="' + product['price'] + '" /></td>';
						html += '	<td class="right">' + product['total'] + '<input type="hidden" name="order_product[' + product_row + '][total]" value="' + product['total'] + '" /><input type="hidden" name="order_product[' + product_row + '][tax]" value="' + product['tax'] + '" /><input type="hidden" name="order_product[' + product_row + '][reward]" value="' + product['reward'] + '" /></td>';
						html += '</tr>';

						product_row++;
					}

					$('#product').html(html);
				} else {
					html = '</tr>';
					html += '	<td colspan="6" class="center"><?= $text_no_results; ?></td>';
					html += '</tr>';

					$('#product').html(html);
				}

				// Vouchers
				if (json['order_voucher'] != '') {
					var voucher_row = 0;

					html = '';

					for (i in json['order_voucher']) {
						voucher = json['order_voucher'][i];

						html += '<tr id="voucher-row' + voucher_row + '">';
						html += '	<td class="center" style="width: 3px;"><img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" title="<?= $button_remove; ?>" alt="<?= $button_remove; ?>" style="cursor: pointer;" onclick="$(\'#voucher-row' + voucher_row + '\').remove(); $(\'#button-update\').trigger(\'click\');" /></td>';
						html += '	<td class="left">' + voucher['description'];
						html += '	<input type="hidden" name="order_voucher[' + voucher_row + '][order_voucher_id]" value="" />';
						html += '	<input type="hidden" name="order_voucher[' + voucher_row + '][voucher_id]" value="' + voucher['voucher_id'] + '" />';
						html += '	<input type="hidden" name="order_voucher[' + voucher_row + '][description]" value="' + voucher['description'] + '" />';
						html += '	<input type="hidden" name="order_voucher[' + voucher_row + '][code]" value="' + voucher['code'] + '" />';
						html += '	<input type="hidden" name="order_voucher[' + voucher_row + '][from_name]" value="' + voucher['from_name'] + '" />';
						html += '	<input type="hidden" name="order_voucher[' + voucher_row + '][from_email]" value="' + voucher['from_email'] + '" />';
						html += '	<input type="hidden" name="order_voucher[' + voucher_row + '][to_name]" value="' + voucher['to_name'] + '" />';
						html += '	<input type="hidden" name="order_voucher[' + voucher_row + '][to_email]" value="' + voucher['to_email'] + '" />';
						html += '	<input type="hidden" name="order_voucher[' + voucher_row + '][voucher_theme_id]" value="' + voucher['voucher_theme_id'] + '" />';
						html += '	<input type="hidden" name="order_voucher[' + voucher_row + '][message]" value="' + voucher['message'] + '" />';
						html += '	<input type="hidden" name="order_voucher[' + voucher_row + '][amount]" value="' + voucher['amount'] + '" />';
						html += '	</td>';
						html += '	<td class="left"></td>';
						html += '	<td class="right">1</td>';
						html += '	<td class="right">' + voucher['amount'] + '</td>';
						html += '	<td class="right">' + voucher['amount'] + '</td>';
						html += '</tr>';

						voucher_row++;
					}

					$('#voucher').html(html);
				} else {
					html = '</tr>';
					html += '	<td colspan="6" class="center"><?= $text_no_results; ?></td>';
					html += '</tr>';

					$('#voucher').html(html);
				}

				// Totals
				if (json['order_product'] != '' || json['order_voucher'] != '' || json['order_total'] != '') {
					html = '';

					if (json['order_product'] != '') {
						for (i = 0; i < json['order_product'].length; i++) {
							product = json['order_product'][i];

							html += '<tr>';
							html += '	<td class="left">' + product['name'] + '<br />';

							if (product['option']) {
								for (j = 0; j < product['option'].length; j++) {
									option = product['option'][j];

									html += '	- <small>' + option['name'] + ': ' + option['value'] + '</small><br />';
								}
							}

							html += '	</td>';
							html += '	<td class="left">' + product['model'] + '</td>';
							html += '	<td class="right">' + product['quantity'] + '</td>';
							html += '	<td class="right">' + product['price'] + '</td>';
							html += '	<td class="right">' + product['total'] + '</td>';
							html += '</tr>';
						}
					}

					if (json['order_voucher'] != '') {
						for (i in json['order_voucher']) {
							voucher = json['order_voucher'][i];

							html += '<tr>';
							html += '	<td class="left">' + voucher['description'] + '</td>';
							html += '	<td class="left"></td>';
							html += '	<td class="right">1</td>';
							html += '	<td class="right">' + voucher['amount'] + '</td>';
							html += '	<td class="right">' + voucher['amount'] + '</td>';
							html += '</tr>';
						}
					}

					var total_row = 0;

					for (i in json['order_total']) {
						total = json['order_total'][i];

						html += '<tr id="total-row' + total_row + '">';
						html += '	<td class="right" colspan="4"><input type="hidden" name="order_total[' + total_row + '][order_total_id]" value="" /><input type="hidden" name="order_total[' + total_row + '][code]" value="' + total['code'] + '" /><input type="hidden" name="order_total[' + total_row + '][title]" value="' + total['title'] + '" /><input type="hidden" name="order_total[' + total_row + '][text]" value="' + total['text'] + '" /><input type="hidden" name="order_total[' + total_row + '][value]" value="' + total['value'] + '" /><input type="hidden" name="order_total[' + total_row + '][sort_order]" value="' + total['sort_order'] + '" />' + total['title'] + ':</td>';
						html += '	<td class="right">' + total['value'] + '</td>';
						html += '</tr>';

						total_row++;
					}

					$('#total').html(html);
				} else {
					html = '</tr>';
					html += '	<td colspan="6" class="center"><?= $text_no_results; ?></td>';
					html += '</tr>';

					$('#total').html(html);
				}

				// Shipping Methods
				if (json['shipping_method']) {
					html = '<option value=""><?= $text_select; ?></option>';

					for (i in json['shipping_method']) {
						html += '<optgroup label="' + json['shipping_method'][i]['title'] + '">';

						if (!json['shipping_method'][i]['error']) {
							for (j in json['shipping_method'][i]['quote']) {
								if (json['shipping_method'][i]['quote'][j]['code'] == $('input[name=\'shipping_code\']').attr('value')) {
									html += '<option value="' + json['shipping_method'][i]['quote'][j]['code'] + '" selected="selected">' + json['shipping_method'][i]['quote'][j]['title'] + '</option>';
								} else {
									html += '<option value="' + json['shipping_method'][i]['quote'][j]['code'] + '">' + json['shipping_method'][i]['quote'][j]['title'] + '</option>';
								}
							}
						} else {
							html += '<option value="" style="color: #F00;" disabled="disabled">' + json['shipping_method'][i]['error'] + '</option>';
						}

						html += '</optgroup>';
					}

					$('select[name=\'shipping\']').html(html);

					if ($('select[name=\'shipping\'] option:selected').attr('value')) {
						$('input[name=\'shipping_method\']').attr('value', $('select[name=\'shipping\'] option:selected').text());
					} else {
						$('input[name=\'shipping_method\']').attr('value', '');
					}

					$('input[name=\'shipping_code\']').attr('value', $('select[name=\'shipping\'] option:selected').attr('value'));
				}

				// Payment Methods
				if (json['payment_method']) {
					html = '<option value=""><?= $text_select; ?></option>';

					for (i in json['payment_method']) {
						if (json['payment_method'][i]['code'] == $('input[name=\'payment_code\']').attr('value')) {
							html += '<option value="' + json['payment_method'][i]['code'] + '" selected="selected">' + json['payment_method'][i]['title'] + '</option>';
						} else {
							html += '<option value="' + json['payment_method'][i]['code'] + '">' + json['payment_method'][i]['title'] + '</option>';
						}
					}

					$('select[name=\'payment\']').html(html);

					if ($('select[name=\'payment\'] option:selected').attr('value')) {
						$('input[name=\'payment_method\']').attr('value', $('select[name=\'payment\'] option:selected').text());
					} else {
						$('input[name=\'payment_method\']').attr('value', '');
					}

					$('input[name=\'payment_code\']').attr('value', $('select[name=\'payment\'] option:selected').attr('value'));
				}
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
//--></script>

<?= $this->builder->js('datepicker'); ?>
	<script type="text/javascript"><!--
		$('.vtabs a').tabs();
//--></script>
<?= $footer; ?>