<?= IS_AJAX ? '' : call('admin/common/header'); ?>
<div class="section">
	<?= breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("URL Aliases"); ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
					href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"> <?= _l("URL Alias:"); ?></td>
							<td><input type="text" name="alias" value="<?= $alias; ?>" size="40"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Path:"); ?></td>
							<td><input type="text" name="path" value="<?= $path; ?>" size="40"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Query:"); ?></td>
							<td><input type="text" name="query" value="<?= $query; ?>" size="40"/></td>
						</tr>
						<tr>
							<td class="required"> <?= _l("Redirect:"); ?></td>
							<td><input type="text" name="redirect" value="<?= $redirect; ?>" size="40"/></td>
						</tr>
						<tr>
							<td><?= _l("Store:"); ?></td>
							<td>
								<?= build('select', array(
									'name'   => 'store_id',
									'data'   => $data_stores,
									'select' => $store_id,
									'key'    => 'store_id',
									'value'  => 'name',
								)); ?>
							</td>
						</tr>
						<tr>
							<td><?= _l("Status:"); ?></td>
							<td><?= build('select', array(
	'name'   => 'status',
	'data'   => $data_statuses,
	'select' => $status
)); ?></td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= IS_AJAX ? '' : call('admin/common/footer'); ?>
