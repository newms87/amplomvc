<?= $header; ?>
<div id="add_block" class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt=""/> <?= _l("New Block"); ?></h1>

			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= _l("Create Block"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"><?= _l("Block Name"); ?></td>
						<td><input type="text" name="name" value="<?= $name; ?>"/></td>
					</tr>
					<tr>
						<td class="required"><?= _l("Block Route"); ?></td>
						<td>
							<input type="text" name="route" value="<?= $route; ?>"/><br/>
							<span class="help"><?= _l("(eg: widget/myblock)"); ?></span>
						</td>
					</tr>
					<tr>
						<td colspan="2"><h2><?= _l("Admin Block Settings"); ?></h2></td>
					</tr>
					<tr>
						<td><?= _l("Include _profile.tpl?"); ?></td>
						<td><input type="checkbox" name="profiles_file" value="1" <?= $profiles_file ? 'checked' : ''; ?> /></td>
					</tr>
					<tr>
						<td><?= _l("Include _settings.tpl?"); ?></td>
						<td><input type="checkbox" name="settings_file" value="1" <?= $settings_file ? 'checked' : ''; ?> /></td>
					</tr>
					<tr>
						<td colspan="2"><h2><?= _l("Store Front"); ?></h2></td>
					</tr>
					<tr>
						<td><?= _l("Theme"); ?></td>
						<td>
							<? $this->builder->setConfig('name', 'name'); ?>
							<?= $this->builder->build('multiselect', $data_themes, 'themes', $themes); ?>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('[name=has_template]').change(function () {
		if ($(this).is(':checked')) {
			$('#template_file_data').slideDown();
		} else {
			$('#template_file_data').hide();
		}
	}).change();
</script>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
