<?= call('admin/common/header'); ?>

<div class="section">
	<?= breadcrumbs(); ?>

	<form action="<?= site_url('admin/catalog/product_class/voucher/update', 'product_id=' . $product_id); ?>" method="post" id="voucher-form" class="box">
		<input type="hidden" name="product_class_id" value="<?= $product_class_id; ?>"/>

		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/product.png'); ?>" alt=""/>
				<?= _l("Voucher"); ?>
			</h1>
			<div class="buttons">
				<button><?= _l("Save"); ?></button>
				<a href="<?= site_url('admin/catalog/product'); ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>

		<div class="section">
			<div id="tabs" class="htabs">
				<a href="#tab-general"><?= _l("Voucher Information"); ?></a>
				<a href="#tab-links"><?= _l("Links"); ?></a>
			</div>

			<div id="tab-general">
				<table class="form">
					<tr>
						<td class="required">
							<?= _l("Name:"); ?></td>
						<td><input type="text" name="name" size="100" value="<?= $name; ?>"/></td>
					</tr>
					<tr>
						<td class="required">
							<?= _l("Voucher ID:"); ?></td>
						<td>
							<input type="text" name="model" value="<?= $model; ?>"/>
							<a class="gen_url" onclick="generate_model(this)"><?= _l("[Generate Model ID]"); ?></a>
						</td>
					</tr>
					<tr>
						<td class="required">
							<div><?= _l("SEO Url:"); ?></div>
							<span class="help"><?= _l("The Search Engine Optimized URL for the product page."); ?></span>
						</td>
						<td>
							<input type="text" onfocus="$(this).ac_msg('error', '<?= _l("<br>Warning! This may cause system instability! Please use the \\'Generate URL\\' button"); ?>');" name="alias" value="<?= $alias; ?>"/>
							<a class="gen_url" onclick="generate_url(this)"><?= _l("[Generate URL]"); ?></a>
						</td>
					</tr>
					<tr>
						<td><?= _l("Teaser: <span class=\"help\">A short teaser about the Gift Card.</span>"); ?></td>
						<td>
							<textarea name="teaser" class="ckedit"><?= $teaser; ?></textarea>
						</td>
					</tr>
					<tr>
						<td><?= _l("Description: <span class=\"help\">This will show up at the top of the Gift Card Product page. You may use full HTML</span>"); ?></td>
						<td>
							<textarea name="description" class="ckedit"><?= $description; ?></textarea>
						</td>
					</tr>
					<tr>
						<td><?= _l("Price:"); ?></td>
						<td><input type="text" name="price" value="<?= $price; ?>"/></td>
					</tr>
					<tr>
						<td><?= _l("Tax Class:"); ?></td>
						<td>
							<?=
							build('select', array(
								'name'   => 'tax_class_id',
								'data'   => $data_tax_classes,
								'select' => $tax_class_id,
								'key'    => 'tax_class_id',
								'value'  => 'title',
							)); ?>
						</td>
					</tr>
					<tr>
						<td><?= _l("Quantity:"); ?></td>
						<td><input type="text" name="quantity" value="<?= $quantity; ?>" size="2"/></td>
					</tr>
					<tr>
						<td><?= _l("Image:"); ?></td>
						<td>
							<input type="text" class="imageinput" name="image" value="<?= $image; ?>"/>
						</td>
					</tr>
					<tr>
						<td><?= _l("Date Available:"); ?></td>
						<td>
							<input type="text" name="date_available" value="<?= $date_available; ?>" size="12" class="datetimepicker"/>
						</td>
					</tr>
					<tr>
						<td><?= _l("Date Expires:"); ?></td>
						<td>
							<input type="text" name="date_expires" value="<?= $date_expires; ?>" size="12" class="datetimepicker"/>
						</td>
					</tr>
					<tr>
						<td><?= _l("Status:"); ?></td>
						<td>
							<?=
							build('select', array(
								'name'   => "status",
								'data'   => $data_statuses,
								'select' => $status
							)); ?>
						</td>
					</tr>
				</table>
			</div>

			<div id="tab-links">
				<table class="form">
					<tr>
						<td><?= _l("Stores:"); ?></td>
						<td>
							<?=
							build('multiselect', array(
								'name'   => "product_stores",
								'data'   => $data_stores,
								'select' => $product_stores,
								'key'    => 'store_id',
								'value'  => 'name',
							)); ?>
						</td>
					</tr>
				</table>
			</div>

		</div>
	</form>
</div>


<script type="text/javascript">
	$.ac_datepicker();

	$('.imageinput').ac_imageinput();

	function generate_url(context) {
		$.ac_msg('clear');

		name = $('input[name=name]').val();

		if (!name) {
			alert("Please make a name for this product before generating the URL");
		}
		else {
			data = {product_id:<?= (int)$product_id; ?>, name: name};
			$(context).fade_post("<?= $url_generate_url; ?>", data, function (json) {
				$('input[name="alias"]').val(json);
			});
		}
	}
	function generate_model(context) {
		name = $('input[name=name]').val();

		if (!name) {
			alert("Please make a name for this product before generating the Model ID");
		} else {
			data = {product_id:<?= $product_id; ?>, name: name};
			$(context).fade_post("<?= $url_generate_model; ?>", data, function (json) {
				$('input[name="model"]').val(json);
			});
		}
	}

	$('#tabs a').tabs();

	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= call('admin/common/footer'); ?>