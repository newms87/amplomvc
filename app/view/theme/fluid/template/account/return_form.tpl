<?= call('common/header'); ?>
<?= area('left'); ?><?= area('right'); ?>
<div class="content">
	<?= breadcrumbs(); ?>
	<?= area('top'); ?>

	<h1><?= _l("Product Returns"); ?></h1>

	<div class="description"><?= _l("<p>Please complete the form below to request an RMA number.</p>"); ?></div>
	<form id="order-lookup" method="post" action="<?= $order_lookup_action; ?>">
		<div class="section">
			<h2><?= _l("Lookup Order Information"); ?></h2>

			<div class="form-item ol-order_id">
				<label for="ol_order_id"><?= _l("Order ID:"); ?></label>
				<input type="text" size="2" name="ol_order_id" value=""/>
			</div>
			<div class="form-item ol-email">
				<label for="ol_email"><?= _l("E-Mail:"); ?></label>
				<input type="text" size="25" name="ol_email" value=""/>
			</div>
			<input type="submit" name="order_lookup" class="button" value="<?= _l("Find Order"); ?>"/>
		</div>
	</form>

	<? if (!empty($return_products)) { ?>
		<form id="return-form" action="<?= $action; ?>" method="post" enctype="multipart/form-data">
			<div class="section">
				<h2><?= _l("Order Information"); ?></h2>

				<div class="order-info order-id">
					<label><?= _l("Order ID:"); ?></label>
					<? if (count($customer_orders) > 1) { ?>
						<? $this->builder->setConfig('order_id', 'display'); ?>
						<?= $this->builder->build('select', $customer_orders, 'order_id', $order_id); ?>
					<? } elseif ($order_id) { ?>
						<span class="value"><?= $order_id; ?></span>
					<? } ?>
					<input type="hidden" name="order_id" value="<?= $order_id; ?>"/>

				</div>

				<div class="order-info date-ordered">
					<label><?= _l("Order Date:"); ?></label>
					<span class="value"><?= $date_ordered_display; ?></span>
					<input type="hidden" name="date_ordered" value="<?= $date_ordered; ?>"/>
				</div>
				<div class="form-item">
					<label for="firstname" class="required"><?= _l("First Name:"); ?></label>
					<input id="firstname" type="text" name="firstname" value="<?= $firstname; ?>"/>
				</div>
				<div class="form-item">
					<label for="lastname" class="required"><?= _l("Last Name:"); ?></label>
					<input id="lastname" type="text" name="lastname" value="<?= $lastname; ?>"/>
				</div>
				<div class="form-item">
					<label for="email" class="required"><?= _l("E-Mail:"); ?></label>
					<input id="email" type="text" name="email" value="<?= $email; ?>"/>
				</div>
				<div class="form-item">
					<label for="telephone" class="required"><?= _l("Telephone:"); ?></label>
					<input id="telephone" type="text" name="telephone" value="<?= $telephone; ?>"/>
				</div>
			</div>

			<div class="section">
				<h2><?= _l("Product Information &amp; Reason for Return"); ?></h2>
				<table class="list return-product">
					<thead>
						<tr>
							<td><?= _l("Product Name"); ?></td>
							<td><?= _l("Model #"); ?></td>
							<td><?= _l("Price"); ?></td>
							<td><?= _l("Return Quantity"); ?></td>
							<td><?= _l("Reason for Return"); ?></td>
							<td><?= _l("Details About Return"); ?></td>
							<td><?= _l("Product is Opened?"); ?></td>
						</tr>
					</thead>
					<tbody>
						<? foreach ($return_products as $product) { ?>
							<? $product_id = $product['product_id']; ?>
							<tr class="return-product">
								<td class="product">
									<?= $product['name']; ?>
									<input type="hidden" name="return_products[<?= $product_id; ?>][product_id]" value="<?= $product_id; ?>"/>
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
										<input type="hidden" name="return_products[<?= $product_id; ?>][return_quantity]" value="0"/>
									<? } else { ?>
										<?= $this->builder->build('select', range(0, (int)$product['quantity']), "return_products[$product_id][return_quantity]", $product['return_quantity']); ?>
									<? } ?>
								</td>
								<td class="reason">
									<? $this->builder->setConfig(false, 'title'); ?>
									<?= $this->builder->build('select', $data_return_reasons, "return_products[$product_id][return_reason_id]", $product['return_reason_id']); ?>
								</td>
								<td class="comment"><textarea name="return_products[<?= $product_id; ?>][comment]"><?= $product['comment']; ?></textarea>
								</td>
								<td
									class="opened"><?= $this->builder->build('select', $data_yes_no, "return_products[$product_id][opened]", $product['opened']); ?></td>
							</tr>
						<? } ?>
					</tbody>
				</table>
			</div>

			<? if (!$order_lookup) { ?>
				<div class="return-captcha">
					<label><?= _l("Enter the code in the box below:"); ?></label>
					<img src="<?= $url_captcha_image; ?>" alt=""/>
					<input type="text" name="captcha" value="<?= $captcha; ?>"/>
				</div>
				<div class="buttons clear">
					<div class="left"><a href="<?= $back; ?>" class="button"><?= _l("Back"); ?></a></div>
					<div class="right">
						<input type="submit" value="<?= _l("Continue"); ?>" class="button"/>
					</div>
				</div>
			<? } ?>
		</form>
	<? }//end if ((!empty($return_products))) ?>

	<?= area('bottom'); ?>
</div>

<script type="text/javascript">
	$('.order_info.order_id select').change(function () {
		location = "<?= $return_product_url; ?>" + '&order_id=' + $(this).val();
	});
</script>

<?= $this->builder->js('datepicker'); ?>
<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>
<?= call('common/footer'); ?>
