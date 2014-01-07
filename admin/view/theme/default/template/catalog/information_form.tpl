<?= $header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'information.png'; ?>" alt=""/> <?= _l("Information"); ?></h1>

			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>
		<div class="section">
			<div id="tabs" class="htabs">
				<a href="#tab-general"><?= _l("General"); ?></a>
				<a href="#tab-design"><?= _l("Design"); ?></a>
			</div>
			<form action="<?= $action; ?>" method="post" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"><?= _l("Information Title:"); ?></td>
							<td><input type="text" name="title" value="<?= $title; ?>" size="100"/></td>
						</tr>
						<tr>
							<td>
								<div><?= _l("SEO Url:"); ?></div>
								<span class="help"><?= _l("The Search Engine Optimized URL for this page."); ?></span>
							</td>
							<td><input type="text" name="alias" value="<?= $alias; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Description:"); ?></td>
							<td><textarea class="ckedit" name="description"><?= $description; ?></textarea></td>
						</tr>
						<tr>
							<td><?= _l("Sort Order:"); ?></td>
							<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1"/></td>
						</tr>
						<tr>
							<td><?= _l("Status:"); ?></td>
							<td><?= $this->builder->build('select', $data_statuses, "status", $status); ?></td>
						</tr>
					</table>
				</div>
				<div id="tab-design">
					<table class="form">
						<tr>
							<td><?= _l("Stores:"); ?></td>
							<td>
								<? $this->builder->setConfig('store_id', 'name'); ?>
								<?= $this->builder->build('multiselect', $data_stores, "stores", $stores); ?>
							</td>
						</tr>
					</table>
					<table class="list">
						<thead>
							<tr>
								<td class="left"><?= _l("Stores:"); ?></td>
								<td class="left"><?= _l("Layout Override:"); ?></td>
							</tr>
						</thead>
						<tbody>
							<? foreach ($data_stores as $store) { ?>
								<tr id="layout_store_<?= $store['store_id']; ?>">
									<td class="left"><?= $store['name']; ?></td>
									<td class="left">
										<? $this->builder->setConfig('layout_id', 'name'); ?>
										<? $select_layout = isset($layouts[$store['store_id']]) ? $layouts[$store['store_id']] : ''; ?>
										<?= $this->builder->build('select', $data_layouts, "layouts[$store[store_id]]", $select_layout); ?>
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
	$('[name="stores[]"]').change(function () {
		if ($(this).is(':checked')) {
			$('#layout_store_' + $(this).val()).show();
		} else {
			$('#layout_store_' + $(this).val()).hide();
		}
	}).change();
</script>

<script type="text/javascript">
	$('#tabs a').tabs();
</script>

<?= $this->builder->js('translations', $translations); ?>
<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
