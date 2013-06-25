<?= $header; ?>
<div class="content">
	<?= $breadcrumbs; ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'information.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<div id="tabs" class="htabs">
				<a href="#tab-general"><?= $tab_general; ?></a>
				<a href="#tab-design"><?= $tab_design; ?></a>
			</div>
			<form action="<?= $action; ?>" method="post" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"><?= $entry_title; ?></td>
							<td><input type="text" name="title" value="<?= $title; ?>" size="100" /></td>
						</tr>
						<tr>
							<td><?= $entry_keyword; ?></td>
							<td><input type="text" name="keyword" value="<?= $keyword; ?>" /></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_description; ?></td>
							<td><textarea class='ckedit' name="description"><?= $description; ?></textarea></td>
						</tr>
						<tr>
							<td><?= $entry_sort_order; ?></td>
							<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1" /></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><?= $this->builder->build('select', $data_statuses, "status", $status); ?></td>
						</tr>
					</table>
				</div>
				<div id="tab-design">
					<table class="form">
						<tr>
							<td><?= $entry_store; ?></td>
							<td>
								<? $this->builder->set_config('store_id', 'name');?>
								<?= $this->builder->build('multiselect', $data_stores, "stores", $stores); ?>
							</td>
						</tr>
					</table>
					<table class="list">
						<thead>
							<tr>
								<td class="left"><?= $entry_store; ?></td>
								<td class="left"><?= $entry_layout; ?></td>
							</tr>
						</thead>
						<tbody>
							<? foreach ($data_stores as $store) { ?>
							<tr id="layout_store_<?= $store['store_id'];?>">
								<td class="left"><?= $store['name']; ?></td>
								<td class="left">
									<? $this->builder->set_config('layout_id', 'name');?>
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

<script type="text/javascript">//<!--
$('[name="stores[]"]').change(function(){
	if ($(this).is(':checked')) {
		$('#layout_store_' + $(this).val()).show();
	} else {
		$('#layout_store_' + $(this).val()).hide();
	}
}).change();
//--></script>

<script type="text/javascript">//<!--
$('#tabs a').tabs();
//--></script>

<?= $this->builder->js('translations', $translations); ?>
<?= $this->builder->js('errors',$errors); ?>

<?= $footer; ?>