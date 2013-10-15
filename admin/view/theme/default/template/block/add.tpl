<?= $header; ?>
<div id="add_block" class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt=""/> <?= $head_title; ?></h1>

			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= $button_create; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"><?= $entry_name; ?></td>
						<td><input type="text" name="name" value="<?= $name; ?>"/></td>
					</tr>
					<tr>
						<td class="required"><?= $entry_route; ?></td>
						<td>
							<input type="text" name="route" value="<?= $route; ?>"/><br/>
							<span class="help"><?= $entry_route_help; ?></span>
						</td>
					</tr>
					<tr>
						<td colspan="2"><h2><?= $text_back_end; ?></h2></td>
					</tr>
					<tr>
						<td><?= $entry_profiles_file; ?></td>
						<td><input type="checkbox" name="profiles_file" value="1" <?= $profiles_file ? 'checked' : ''; ?> /></td>
					</tr>
					<tr>
						<td><?= $entry_settings_file; ?></td>
						<td><input type="checkbox" name="settings_file" value="1" <?= $settings_file ? 'checked' : ''; ?> /></td>
					</tr>
					<tr>
						<td colspan="2"><h2><?= $text_front_end; ?></h2></td>
					</tr>
					<tr>
						<td><?= $entry_theme; ?></td>
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

<script type="text/javascript">//<!--
	$('[name=has_template]').change(function () {
		if ($(this).is(':checked')) {
			$('#template_file_data').slideDown();
		} else {
			$('#template_file_data').hide();
		}
	}).change();
//--></script>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
