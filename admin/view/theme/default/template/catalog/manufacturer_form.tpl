<?= $common_header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'shipping.png'; ?>" alt=""/> <?= _l("Manufacturer"); ?></h1>

			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>
		<div class="section">
			<div id="tabs" class="htabs">
				<a href="#tab-general"><?= _l("General"); ?></a>
			</div>
			<form action="<?= $action; ?>" method="post" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"> <?= _l("Manufacturer Name:"); ?></td>
							<td><input type="text" name="name" value="<?= $name; ?>" size="100"/>
							</td>
						</tr>
						<tr>
							<td class="required">
								<div><?= _l("SEO Url:"); ?></div>
								<span class="help"><?= _l("The Search Engine Optimized URL for this page."); ?></span>
							</td>
							<td>
								<input type="text" onfocus="generate_url_warning(this)" name="keyword" value="<?= $keyword; ?>"/>
								<a class="gen_url" onclick="generate_url(this)"><?= _l("[Generate URL]"); ?></a>
							</td>
						</tr>
						<tr>
							<td><?= _l("Teaser:"); ?></td>
							<td><input type="text" name="teaser" value="<?= $teaser; ?>" size="80"/></td>
						</tr>
						<tr>
							<td><?= _l("Description:"); ?></td>
							<td><textarea name="description" class="ckedit"/><?= $description; ?></textarea></td>
						</tr>
						<tr>
							<td><?= _l("Default Shipping Return Policy"); ?></td>
							<td><textarea name="shipping_return" class="ckedit"/><?= $shipping_return; ?></textarea></td>
						</tr>
						<tr>
							<td><?= _l("Stores:"); ?></td>
							<td>
								<? $this->builder->setConfig('store_id', 'name'); ?>
								<?= $this->builder->build('multiselect', $data_stores, "stores", $stores); ?>
							</td>
						</tr>
						<tr>
							<td><?= _l("Image:"); ?></td>
							<td>
								<input type="text" class="imageinput" name="image" value="<?= $image; ?>" />
							</td>
						</tr>
						<tr>
							<td><?= _l("Active On:"); ?></td>
							<td><input type="text" class="datetimepicker" name="date_active" value="<?= $date_active; ?>"/>
							</td>
						</tr>
						<tr>
							<td><?= _l("Expires On <span class=\"help\">Leave blank if this Manufacturer does not expire</span>:"); ?></td>
							<td><input type="text" class="datetimepicker" name="date_expires" value="<?= $date_expires; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Sort Order:"); ?></td>
							<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1"/></td>
						</tr>
						<tr>
							<td><?= _l("Status:"); ?></td>
							<td><?= $this->builder->build('select', $data_statuses, 'status', $status); ?></td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('ckeditor'); ?>

<script type="text/javascript">
	$('.datetimepicker').ac_datepicker();

	function generate_url_warning(field) {
		if ($('#gen_warn').length == 0)
			$(field).parent().append('<span id="gen_warn" style="color:red"><?= _l("Warning! This may cause system instability! Please use the \\\'Generate URL\\\' button"); ?></span>');
	}
	function generate_url(c) {
		$(c).fadeOut(500, function () {
			$(this).show();
		});
		$('#gen_warn').remove();
		name = $('input[name="name"]').val();
		if (!name)
			alert("Please make a name for this Manufacturer before generating the URL");
		$.post("<?= $url_generate_url; ?>", {manufacturer_id:<?= $manufacturer_id?$manufacturer_id:0; ?>, name: name}, function (json) {
			$('input[name="keyword"]').val(json);
		}, 'json');
	}

	$('.imageinput').ac_imageinput();

	$('#tabs a').tabs();
</script>

<?= $this->builder->js('errors', $errors); ?>
<?= $this->builder->js('translations', $translations); ?>

<?= $common_footer; ?>
