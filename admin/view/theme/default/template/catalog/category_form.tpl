<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'category.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
					<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
				</div>
			</div>
			<div class="section">
				<div id="tabs" class="htabs">
					<a href="#tab-general"><?= $tab_general; ?></a>
					<a href="#tab-data"><?= $tab_data; ?></a>
					<a href="#tab-design"><?= $tab_design; ?></a>
				</div>

				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<div id="tab-general">
						<table class="form">
							<tr>
								<td class="required"> <?= $entry_name; ?></td>
								<td><input type="text" name="name" size="60" value="<?= $name; ?>"/></td>
							</tr>
							<tr>
								<td><?= $entry_meta_keyword; ?></td>
								<td><textarea name="meta_keywords" rows="4" cols="60"><?= $meta_keywords; ?></textarea></td>
							</tr>
							<tr>
								<td><?= $entry_meta_description; ?></td>
								<td><textarea name="meta_description" rows="8" cols="60"><?= $meta_description; ?></textarea>
								</td>
							</tr>
							<tr>
								<td><?= $entry_description; ?></td>
								<td><textarea class="ckedit" name="description"><?= $description; ?></textarea></td>
							</tr>
						</table>
					</div>
					<div id="tab-data">
						<table class="form">
							<tr>
								<td><?= $entry_parent; ?></td>
								<? $this->builder->set_config('category_id', 'pathname'); ?>
								<td><?= $this->builder->build('select', $data_categories, 'parent_id', (int)$parent_id); ?></td>
							</tr>
							<tr>
								<td><?= $entry_store; ?></td>
								<? $this->builder->set_config('store_id', 'name'); ?>
								<td><?= $this->builder->build('multiselect', $data_stores, "category_store", $stores); ?></td>
							</tr>
							<tr>
								<td><?= $entry_alias; ?></td>
								<td>
									<input type="text" onfocus="$(this).next().display_error('<?= $warning_generate_url; ?>', 'gen_url');" name="alias" value="<?= $alias; ?>"/>
									<a class="gen_url" onclick="generate_url($(this))"><?= $button_generate_url; ?></a>
								</td>
							</tr>
							<tr>
								<td><?= $entry_image; ?></td>
								<td>
									<?= $this->builder->set_builder_template('click_image'); ?>
									<?= $this->builder->image_input("image", $image); ?>
								</td>
							</tr>
							<tr>
								<td><?= $entry_sort_order; ?></td>
								<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1"/></td>
							</tr>
							<tr>
								<td><?= $entry_status; ?></td>
								<td><?= $this->builder->build('select', $statuses, 'status', (int)$status); ?></td>
							</tr>
						</table>
					</div>
					<div id="tab-design">
						<table class="list">
							<thead>
							<tr>
								<td class="left"><?= $entry_store; ?></td>
								<td class="left"><?= $entry_layout; ?></td>
							</tr>
							</thead>
							<tbody>
							<? $this->builder->set_config('layout_id', 'name'); ?>

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

<script type="text/javascript">//<!--
function generate_url(context) {
	$.clear_errors('gen_url');

	name = $('input[name=name]').val();

	if (!name) {
		alert("Please make a name for this Category before generating the URL");
		return;
	}

	data = {category_id: <?= (int)$category_id; ?>, name: name};

	$(context).fade_post("<?= $url_generate_url; ?>", data, function (response) {
		$('input[name="alias"]').val(response);
	});
}

//Tabs
$('#tabs a').tabs();
//--></script>

<?= $this->builder->js('errors', $errors); ?>
<?= $this->builder->js('translations', $translations); ?>

<?= $footer; ?>