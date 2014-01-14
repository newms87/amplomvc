<?= $header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<? if (_l("Warning: Please check the form carefully for errors!")) { ?>
		<div class="message_box warning"><?= _l("Warning: Please check the form carefully for errors!"); ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'customer.png'; ?>" alt=""/> <?= _l("Product Returns"); ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
					href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
		</div>
		<div class="section">
			<div class="htabs"><a href="#tab-return"><?= _l("Return Details"); ?></a><a
					href="#tab-product"><?= _l("Products"); ?></a></div>
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-return">
					<table class="form">
						<tr>
							<td class="required"> <?= _l("Order ID:"); ?></td>
							<td><input type="text" name="order_id" value="<?= $order_id; ?>"/>
								<? if (_l("Order ID required!")) { ?>
									<span class="error"><?= _l("Order ID required!"); ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td><?= _l("Order Date:"); ?></td>
							<td><input type="text" name="date_ordered" value="<?= $date_ordered; ?>" class="datepicker"/>
							</td>
						</tr>
						<tr>
							<td><?= _l("Customer:"); ?></td>
							<td><input type="text" name="customer" value="<?= $customer; ?>"/>
								<input type="hidden" name="customer_id" value="<?= $customer_id; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("First Name:"); ?></td>
							<td><input type="text" name="firstname" value="<?= $firstname; ?>"/>
								<? if (_l("First Name must be between 1 and 32 characters!")) { ?>
									<span class="error"><?= _l("First Name must be between 1 and 32 characters!"); ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Last Name:"); ?></td>
							<td><input type="text" name="lastname" value="<?= $lastname; ?>"/>
								<? if (_l("Last Name must be between 1 and 32 characters!")) { ?>
									<span class="error"><?= _l("Last Name must be between 1 and 32 characters!"); ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("E-Mail:"); ?></td>
							<td><input type="text" name="email" value="<?= $email; ?>"/>
								<? if (_l("E-Mail Address does not appear to be valid!")) { ?>
									<span class="error"><?= _l("E-Mail Address does not appear to be valid!"); ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Telephone:"); ?></td>
							<td><input type="text" name="telephone" value="<?= $telephone; ?>"/>
								<? if (_l("Telephone must be between 3 and 32 characters!")) { ?>
									<span class="error"><?= _l("Telephone must be between 3 and 32 characters!"); ?></span>
								<? } ?></td>
						</tr>
					</table>
				</div>
				<div id="tab-product">
					<table class="form">
						<tr>
							<td class="required"> <?= _l("Product:<br /><span class=\"help\">(Autocomplete)</span>"); ?></td>
							<td><input type="text" name="product" value="<?= $subscription; ?>"/>
								<input type="hidden" name="product_id" value="<?= $product_id; ?>"/>
								<? if (_l("Product Name must be greater than 3 and less than 255 characters!")) { ?>
									<span class="error"><?= _l("Product Name must be greater than 3 and less than 255 characters!"); ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td><?= _l("Model:"); ?></td>
							<td><input type="text" name="model" value="<?= $model; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Quantity:"); ?></td>
							<td><input type="text" name="quantity" value="<?= $quantity; ?>" size="3"/></td>
						</tr>
						<tr>
							<td><?= _l("Return Reason:"); ?></td>
							<td><select name="return_reason_id">
									<? foreach ($return_reasons as $return_reason) { ?>
										<? if ($return_reason['return_reason_id'] == $return_reason_id) { ?>
											<option value="<?= $return_reason['return_reason_id']; ?>"
												selected="selected"><?= $return_reason['name']; ?></option>
										<? } else { ?>
											<option value="<?= $return_reason['return_reason_id']; ?>"><?= $return_reason['name']; ?></option>
										<? } ?>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= _l("Opened:"); ?></td>
							<td><select name="opened">
									<? if ($opened) { ?>
										<option value="1" selected="selected"><?= _l("Opened"); ?></option>
										<option value="0"><?= _l("Unopened"); ?></option>
									<? } else { ?>
										<option value="1"><?= _l("Opened"); ?></option>
										<option value="0" selected="selected"><?= _l("Unopened"); ?></option>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= _l("Comment:"); ?></td>
							<td><textarea name="comment" cols="40" rows="5"><?= $comment; ?></textarea></td>
						</tr>
						<tr>
							<td><?= _l("Return Action:"); ?></td>
							<td><select name="return_action_id">
									<option value="0"></option>
									<? foreach ($return_actions as $return_action) { ?>
										<? if ($return_action['return_action_id'] == $return_action_id) { ?>
											<option value="<?= $return_action['return_action_id']; ?>"
												selected="selected"> <?= $return_action['name']; ?></option>
										<? } else { ?>
											<option value="<?= $return_action['return_action_id']; ?>"><?= $return_action['name']; ?></option>
										<? } ?>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= _l("Return Status:"); ?></td>
							<td><?= $this->builder->build('select', $data_return_statuses, 'return_status_id', $return_status_id); ?></td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript"><
	!--
		$.widget('custom.catcomplete', $.ui.autocomplete, {
			_renderMenu: function (ul, items) {
				var self = this, currentCategory = '';

				$.each(items, function (index, item) {
					if (item.category != currentCategory) {
						ul.append('<li class="ui-autocomplete-category">' + item.category + '</li>');

						currentCategory = item.category;
					}

					self._renderItem(ul, item);
				});
			}
		});

	$('input[name=\'customer\']').catcomplete({
		delay: 0,
		source: function (request, response) {
			$.ajax({
				url: "<?= HTTP_ADMIN . "index.php?route=sale/customer/autocomplete"; ?>" + '&filter_name=" + encodeURIComponent(request.term),
				dataType: "json',
				success: function (json) {
					response($.map(json, function (item) {
						return {
							category: item.customer_group,
							label: item.name,
							value: item.customer_id,
							firstname: item.firstname,
							lastname: item.lastname,
							email: item.email,
							telephone: item.telephone
						}
					}));
				}
			});

		},
		select: function (event, ui) {
			$('input[name=\'customer\']').attr('value', ui.item.label);
			$('input[name=\'customer_id\']').attr('value', ui.item.value);
			$('input[name=\'firstname\']').attr('value', ui.item.firstname);
			$('input[name=\'lastname\']').attr('value', ui.item.lastname);
			$('input[name=\'email\']').attr('value', ui.item.email);
			$('input[name=\'telephone\']').attr('value', ui.item.telephone);

			return false;
		}
	});
</script>
<script type="text/javascript"><
	!--
		$('input[name=\'product\']').autocomplete({
			delay: 0,
			source: function (request, response) {
				$.ajax({
					url: "<?= HTTP_ADMIN . "index.php?route=catalog/product/autocomplete"; ?>" + '&filter_name=" + encodeURIComponent(request.term),
					dataType: "json',
					success: function (json) {
						response($.map(json, function (item) {
							return {
								label: item.name,
								value: item.product_id,
								model: item.model
							}
						}));
					}
				});
			},
			select: function (event, ui) {
				$('input[name=\'product_id\']').attr('value', ui.item.value);
				$('input[name=\'product\']').attr('value', ui.item.label);
				$('input[name=\'model\']').attr('value', ui.item.model);

				return false;
			}
		});
</script>
<script type="text/javascript"><
	!--
		$(document).ready(function () {
			$('.date').datepicker({dateFormat: 'yy-mm-dd'});
		});
</script>
<script type="text/javascript"><
	!--
		$('.htabs a').tabs();
</script>
<?= $footer; ?>
