<?= call('common/header'); ?>
	<div class="section">
		<?= breadcrumbs(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("DB Rules"); ?></h1>

				<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
						href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
			</div>
			<div class="section">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<div id="tab-general">
						<table class="form">
							<tr>
								<td class="required"> <?= _l("Table:"); ?></td>
								<td><input type="text" name="table" value="<?= $table; ?>" size="40"/></td>
							</tr>
							<tr>
								<td class="required"> <?= _l("Field Name (Column):"); ?></td>
								<td><input type="text" name="column" value="<?= $column; ?>" size="40"/>
							</tr>
							<tr>
								<td><?= _l("Escape Method:"); ?></td>
								<td><?= $this->builder->build('select', $data_escape_types, 'escape_type', (int)$escape_type); ?></td>
							</tr>
							<tr>
								<td><?= _l("Truncate Table Allowed?:"); ?></td>
								<td><?= $this->builder->build('select', $data_yes_no, 'truncate', (int)$truncate); ?></td>
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

<?= call('common/footer'); ?>
