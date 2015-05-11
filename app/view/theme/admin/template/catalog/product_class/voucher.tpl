<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<form action="<?= site_url('admin/product/product_class/voucher/update', 'product_id=' . $product_id); ?>" method="post" id="voucher-form" class="box">
		<input type="hidden" name="product_class_id" value="<?= $product_class_id; ?>"/>

		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/product.png'); ?>" alt=""/>
				{{Voucher}}
			</h1>

			<div class="buttons">
				<button>{{Save}}</button>
				<a href="<?= site_url('admin/product/product'); ?>" class="button">{{Cancel}}</a>
			</div>
		</div>

		<div class="section">
			<table class="form">
				<tr>
					<td class="required">
						{{Name:}}
					</td>
					<td><input type="text" name="name" size="100" value="<?= $name; ?>"/></td>
				</tr>
				<tr>
					<td class="required">
						{{Voucher ID:}}
					</td>
					<td>
						<input type="text" name="model" value="<?= $model; ?>"/>
						<a class="gen-btn gen-model">{{[Generate Model ID]}}</a>
					</td>
				</tr>
				<tr>
					<td class="required">
						<div>{{SEO Url:}}</div>
						<span class="help">{{The Search Engine Optimized URL for the product page.}}</span>
					</td>
					<td>
						<input type="text" onfocus="$(this).show_msg('error', '{{<br>Warning! This may cause system instability! Please use the \\'Generate URL\\' button}}');" name="alias" value="<?= $alias; ?>"/>
						<a class="gen-btn gen-seo-url">{{[Generate URL]}}</a>
					</td>
				</tr>
				<tr>
					<td><?= _l("Teaser: <span class=\"help\">A short teaser about the Gift Card.</span>"); ?></td>
					<td>
						<textarea name="teaser"><?= $teaser; ?></textarea>
					</td>
				</tr>
				<tr>
					<td><?= _l("Description: <span class=\"help\">This will show up at the top of the Gift Card Product page. You may use full HTML</span>"); ?></td>
					<td>
						<textarea name="description"><?= $description; ?></textarea>
					</td>
				</tr>
				<tr>
					<td>{{Price:}}</td>
					<td><input type="text" name="price" value="<?= $price; ?>"/></td>
				</tr>
				<tr>
					<td>{{Tax Class:}}</td>
					<td>
						<?=
						build(array(
							'type'   => 'select',
							'name'   => 'tax_class_id',
							'data'   => $data_tax_classes,
							'select' => $tax_class_id,
							'value'  => 'tax_class_id',
							'label'  => 'title',
						)); ?>
					</td>
				</tr>
				<tr>
					<td>{{Quantity:}}</td>
					<td><input type="text" name="quantity" value="<?= $quantity; ?>" size="2"/></td>
				</tr>
				<tr>
					<td>{{Image:}}</td>
					<td>
						<input type="text" class="imageinput" name="image" value="<?= $image; ?>"/>
					</td>
				</tr>
				<tr>
					<td>{{Date Available:}}</td>
					<td>
						<input type="text" name="date_available" value="<?= $date_available; ?>" size="12" class="datetimepicker"/>
					</td>
				</tr>
				<tr>
					<td>{{Date Expires:}}</td>
					<td>
						<input type="text" name="date_expires" value="<?= $date_expires; ?>" size="12" class="datetimepicker"/>
					</td>
				</tr>
				<tr>
					<td>{{Status:}}</td>
					<td>
						<?=
						build(array(
							'type'   => 'select',
							'name'   => "status",
							'data'   => $data_statuses,
							'select' => $status
						)); ?>
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>


<script type="text/javascript">
	$.ac_datepicker();

	$('.imageinput').ac_imageinput();

	$('.gen-model').click(function(){
		var name = $('input[name=name]').val();

		if (!name) {
			alert("Please make a name for this product before generating the Model ID");
		} else {
			data = {product_id:<?= $product_id; ?>, name: name};
			$(context).fade_post("<?= $url_generate_model; ?>", data, function (json) {
				$('input[name="model"]').val(json);
			});
		}
	});

	$('.gen-seo-url').click(function(){
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
	});

	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
