<?= _call('common/header'); ?>
<div class="section">
	<?= _breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/customer.png'); ?>" alt=""/> <?= _l("Coupon"); ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
					href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
		</div>
		<div class="section">
			<div id="tabs" class="htabs"><a href="#tab-general"><?= _l("General"); ?></a>
				<? if ($coupon_id) { ?>
					<a href="#tab-history"><?= _l("Coupon History"); ?></a>
				<? } ?>
			</div>
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"> <?= _l("Coupon Name:"); ?></td>
							<td><input name="name" value="<?= $name; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Code:<br /><span class=\"help\">The code the customer enters to get the discount</span>"); ?></td>
							<td><input type="text" name="code" value="<?= $code; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Type:<br /><span class=\"help\">Percentage or Fixed Amount</span>"); ?></td>
							<td><?= $this->builder->build('select', $data_types, 'type', $type); ?></td>
						</tr>
						<tr>
							<td><?= _l("Discount:"); ?></td>
							<td><input type="text" name="discount" value="<?= $discount; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Total Amount:<br /><span class=\"help\">The total amount that must reached before the coupon is valid.</span>"); ?></td>
							<td><input type="text" name="total" value="<?= $total; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Customer Login:<br /><span class=\"help\">Customer must be logged in to use the coupon.</span>"); ?></td>
							<td><?= $this->builder->build('radio', $data_yes_no, 'logged', (int)$logged); ?></td>
						</tr>
						<tr>
							<td><?= _l("Free Shipping:"); ?></td>
							<td>
								<div><?= $this->builder->build('radio', $data_yes_no, 'shipping', (int)$shipping, array('onclick' => "if($(this).find(':checked').val() == '1')$('#coupon_ship_geozone').show(); else $('#coupon_ship_geozone').hide();")); ?></div>
								<div <?= (int)$shipping ? '' : "style=\"display:none\""; ?> id="coupon_ship_geozone">
									<? $this->builder->setConfig('geo_zone_id', 'name'); ?>
									<?= $this->builder->build('select', $data_geo_zones, 'shipping_geozone', (int)$shipping_geozone); ?>
								</div>
							</td>
						</tr>
						<tr>
							<td><?= _l("Customers:"); ?></td>
							<td>
								<? foreach ($coupon_customers as $cc) { ?>
									<div><?= $cc['lastname'] . ', ' . $cc['firstname'] . ' - ' . $cc['email']; ?></div>
									<input type="hidden" name="coupon_customers[]" value="<?= $cc['customer_id']; ?>"/>
								<? } ?>
							</td>
						</tr>
						<tr>
							<td><?= _l("Category:<br /><span class=\"help\">Choose all products under selected category.</span>"); ?></td>
							<td>
								<div class="scrollbox">
									<? $class = 'odd'; ?>
									<? foreach ($categories as $category) { ?>
										<? $class = ($class == 'even' ? 'odd' : 'even'); ?>
										<div class="<?= $class; ?>">
											<input type="checkbox" name="categories[]" value="<?= $category['category_id']; ?>"/>
											<?= $category['name']; ?> </div>
									<? } ?>
								</div>
							</td>
						</tr>
						<tr>
							<td><?= _l("Products:<br /><span class=\"help\">Choose specific products the coupon will apply to. Select no products to apply coupon to entire cart.</span>"); ?></td>
							<td><input type="text" name="product" value=""/></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<div id="coupon-product" class="scrollbox">
									<? foreach ($coupon_products as $coupon_product) { ?>
										<div id="coupon-product<?= $coupon_product['product_id']; ?>">
											<?= $coupon_product['name']; ?>
											<img src="<?= theme_url('image/delete.png'); ?>"/>
											<input type="hidden" name="coupon_products[]" value="<?= $coupon_product['product_id']; ?>"/>
										</div>
									<? } ?>
								</div>
							</td>
						</tr>
						<tr>
							<td><?= _l("Date Start:"); ?></td>
							<td><input type="text" name="date_start" class="datetimepicker" value="<?= $date_start; ?>"
									size="12" id="date-start"/></td>
						</tr>
						<tr>
							<td><?= _l("Date End:"); ?></td>
							<td><input type="text" name="date_end" class="datetimepicker" value="<?= $date_end; ?>"
									size="12" id="date-end"/></td>
						</tr>
						<tr>
							<td><?= _l("Uses Per Coupon:<br /><span class=\"help\">The maximum number of times the coupon can be used by any customer. Leave blank for unlimited</span>"); ?></td>
							<td><input type="text" name="uses_total" value="<?= $uses_total; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Uses Per Customer:<br /><span class=\"help\">The maximum number of times the coupon can be used by a single customer. Leave blank for unlimited</span>"); ?></td>
							<td><input type="text" name="uses_customer" value="<?= $uses_customer; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Status:"); ?></td>
							<td><?= $this->builder->build('select', $data_statuses, 'status', $status); ?></td>
						</tr>
					</table>
				</div>
				<? if ($coupon_id) { ?>
					<div id="tab-history">
						<div id="history"></div>
					</div>
				<? } ?>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('datepicker'); ?>

<? if ($coupon_id) { ?>
	<script type="text/javascript">
		$('#history .pagination a').live('click', function () {
			$('#history').load(this.href);

			return false;
		});

		$('#history').load("<?= $url_coupon_history; ?>" + '&coupon_id=<?= $coupon_id; ?>');
	</script>
<? } ?>
<script type="text/javascript">
	$('#tabs a').tabs();
</script>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>
<?= _call('common/footer'); ?>
