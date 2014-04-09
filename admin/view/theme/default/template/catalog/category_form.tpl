<?= $common_header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'category.png'; ?>" alt=""/> <?= _l("Category"); ?></h1>

			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>
		<div class="section">
			<div id="tabs" class="htabs">
				<a href="#tab-general"><?= _l("General"); ?></a>
				<a href="#tab-data"><?= _l("Data"); ?></a>
				<a href="#tab-design"><?= _l("Design"); ?></a>
			</div>

			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"> <?= _l("Category Name:"); ?></td>
							<td><input type="text" name="name" size="60" value="<?= $name; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Meta Tag Keywords:"); ?></td>
							<td><textarea name="meta_keywords" rows="4" cols="60"><?= $meta_keywords; ?></textarea></td>
						</tr>
						<tr>
							<td><?= _l("Meta Tag Description:"); ?></td>
							<td><textarea name="meta_description" rows="8" cols="60"><?= $meta_description; ?></textarea>
							</td>
						</tr>
						<tr>
							<td><?= _l("Description:"); ?></td>
							<td><textarea class="ckedit" name="description"><?= $description; ?></textarea></td>
						</tr>
					</table>
				</div>
				<div id="tab-data">
					<table class="form">
						<tr>
							<td><?= _l("Parent Category:"); ?></td>
							<? $this->builder->setConfig('category_id', 'pathname'); ?>
							<td><?= $this->builder->build('select', $data_categories, 'parent_id', (int)$parent_id); ?></td>
						</tr>
						<tr>
							<td><?= _l("Stores:"); ?></td>
							<? $this->builder->setConfig('store_id', 'name'); ?>
							<td><?= $this->builder->build('multiselect', $data_stores, "category_store", $stores); ?></td>
						</tr>
						<tr>
							<td><?= _l("SEO Url:<span class=\"help\">The Search Engine Optimized alias for this category.</span>"); ?></td>
							<td>
								<input type="text" onfocus="$(this).next().display_error('<?= _l("Warning! This may cause system instability! Please use the \\\'Generate URL\\\' button"); ?>', 'gen_url');" name="alias" value="<?= $alias; ?>"/>
								<a class="gen_url" onclick="generate_url($(this))"><?= _l("[Generate URL]"); ?></a>
							</td>
						</tr>
						<tr>
							<td><?= _l("Image:"); ?></td>
							<td>
								<input type="text" class="imageinput" name="image" value="<?= $image; ?>" />
							</td>
						</tr>
						<tr>
							<td><?= _l("Sort Order:"); ?></td>
							<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1"/></td>
						</tr>
						<tr>
							<td><?= _l("Status:"); ?></td>
							<td><?= $this->builder->build('select', $data_statuses, 'status', (int)$status); ?></td>
						</tr>
					</table>
				</div>
				<div id="tab-design">
					<table class="list">
						<thead>
							<tr>
								<td class="left"><?= _l("Stores:"); ?></td>
								<td class="left"><?= _l("Layout Override:"); ?></td>
							</tr>
						</thead>
						<tbody>
							<? $this->builder->setConfig('layout_id', 'name'); ?>

							<? foreach ($data_stores as $store) { ?>
								<tr>
									<td class="left"><?= $store['name']; ?></td>
									<td class="left">
										<?= $this->builder->build('select', $data_layouts, "layouts[$store[store_id]][layout_id]", isset($layouts[$store['store_id']]) ? (int)$layouts[$store['store_id']] : ''); ?>
									</td>
								</tr>
							<? } ?>
						</tbody>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('ckeditor'); ?>

<script type="text/javascript">
	function generate_url(context) {
		$.clear_errors('gen_url');

		var name = $('input[name=name]').val();

		if (!name) {
			alert("Please make a name for this Category before generating the URL");
			return;
		}

		data = {category_id: <?= (int)$category_id; ?>, name: name};

		$(context).fade_post("<?= $url_generate_url; ?>", data, function (response) {
			$('input[name="alias"]').val(response);
		});
	}

	$('.imageinput').ac_imageinput();

	//Tabs
	$('#tabs a').tabs();
</script>

<?= $this->builder->js('errors', $errors); ?>
<?= $this->builder->js('translations', $translations); ?>

<?= $common_footer; ?>
