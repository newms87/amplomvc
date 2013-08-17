<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
	<div id="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $content_top; ?>

		<h1><?= $head_title; ?></h1>

		<div class="description"><?= $text_description; ?></div>
		<form id="order_lookup" method="post" action="<?= $order_lookup_action; ?>">
			<div class="section">
				<h2><?= $text_order_lookup; ?></h2>

				<div class="form_item ol_order_id">
					<label for="ol_order_id"><?= $entry_order_id; ?></label>
					<input type="text" size="2" name="ol_order_id" value=""/>
				</div>
				<div class="form_item ol_email">
					<label for="ol_email"><?= $entry_email; ?></label>
					<input type="text" size="25" name="ol_email" value=""/>
				</div>
				<input type="submit" name="order_lookup" class="button" value="<?= $button_order_lookup; ?>"/>
			</div>
		</form>

		<? if (!empty($return_products)) { ?>
			<form id="return_form" action="<?= $action; ?>" method="post" enctype="multipart/form-data">
				<div class="section">
					<h2><?= $text_order; ?></h2>

					<div class="order_info order_id">
						<label><?= $entry_order_id; ?></label>
						<? if (count($customer_orders) > 1) { ?>
							<? $this->builder->set_config('order_id', 'display'); ?>
							<?= $this->builder->build('select', $customer_orders, 'order_id', $order_id); ?>
						<? } elseif ($order_id) { ?>
							<span class="value"><?= $order_id; ?></span>
						<? } ?>
						<input type="hidden" name="order_id" value="<?= $order_id; ?>"/>

					</div>

					<div class="order_info date_ordered">
						<label><?= $entry_date_ordered; ?></label>
						<span class="value"><?= $date_ordered_display; ?></span>
						<input type="hidden" name="date_ordered" value="<?= $date_ordered; ?>"/>
					</div>
					<div class="form_item">
						<label for="firstname" class="required"><?= $entry_firstname; ?></label>
						<input id="firstname" type="text" name="firstname" value="<?= $firstname; ?>"/>
					</div>
					<div class="form_item">
						<label for="lastname" class="required"><?= $entry_lastname; ?></label>
						<input id="lastname" type="text" name="lastname" value="<?= $lastname; ?>"/>
					</div>
					<div class="form_item">
						<label for="email" class="required"><?= $entry_email; ?></label>
						<input id="email" type="text" name="email" value="<?= $email; ?>"/>
					</div>
					<div class="form_item">
						<label for="telephone" class="required"><?= $entry_telephone; ?></label>
						<input id="telephone" type="text" name="telephone" value="<?= $telephone; ?>"/>
					</div>
				</div>

				<div class="section">
					<h2><?= $text_product; ?></h2>
					<table class="list return_product">
						<thead>
						<tr>
							<td><?= $column_return_product; ?></td>
							<td><?= $column_return_model; ?></td>
							<td><?= $column_return_price; ?></td>
							<td><?= $column_return_quantity; ?></td>
							<td><?= $column_return_reason; ?></td>
							<td><?= $column_return_comment; ?></td>
							<td><?= $column_return_opened; ?></td>
						</tr>
						</thead>
						<tbody>
						<? foreach ($return_products as $product) { ?>
							<? $product_id = $product['product_id']; ?>
							<tr class="return_product">
								<td class="product">
									<?= $product['name']; ?>
									<input type="hidden" name="return_products[<?= $product_id; ?>][product_id]"
									       value="<?= $product_id; ?>"/>
								</td>
								<td class="model">
									<?= $product['model']; ?>
								</td>
								<td class="price">
									<?= $product['price']; ?>
								</td>
								<td class="quantity">
									<? if (!empty($product['no_return'])) { ?>
										<span><?= $product['no_return']; ?></span>
										<input type="hidden" name="return_products[<?= $product_id; ?>][return_quantity]"
										       value="0"/>
									<? } else { ?>
										<?= $this->builder->build('select', range(0, (int)$product['quantity']), "return_products[$product_id][return_quantity]", $product['return_quantity']); ?>
									<? } ?>
								</td>
								<td class="reason">
									<? $this->builder->set_config(false, 'title'); ?>
									<?= $this->builder->build('select', $data_return_reasons, "return_products[$product_id][return_reason_id]", $product['return_reason_id']); ?>
								</td>
								<td class="comment"><textarea
										name="return_products[<?= $product_id; ?>][comment]"><?= $product['comment']; ?></textarea>
								</td>
								<td
									class="opened"><?= $this->builder->build('select', $data_yes_no, "return_products[$product_id][opened]", $product['opened']); ?></td>
							</tr>
						<? } ?>
						</tbody>
					</table>
				</div>

				<? if (!$order_lookup) { ?>
					<div class="return_captcha">
						<label><?= $entry_captcha; ?></label>
						<img src="<?= $url_captcha_image; ?>" alt=""/>
						<input type="text" name="captcha" value="<?= $captcha; ?>"/>
					</div>
					<div class="clear"></div>
					<div class="buttons">
						<div class="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></div>
						<div class="right">
							<input type="submit" value="<?= $button_continue; ?>" class="button"/>
						</div>
					</div>
				<? } ?>
			</form>
		<? }//end if ((!empty($return_products))) ?>

		<?= $content_bottom; ?>
	</div>

	<script type="text/javascript">//<!--
		$('.order_info.order_id select').change(function () {
			location = "<?= $return_product_url; ?>" + '&order_id=' + $(this).val();
		});
		//--></script>

<?= $this->builder->js('datepicker'); ?>
<?= $this->builder->js('errors', $errors); ?>
<?= $footer; ?>