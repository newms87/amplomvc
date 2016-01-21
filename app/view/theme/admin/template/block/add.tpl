<?= $is_ajax ? '' : call('admin/header'); ?>
<div id="add_block" class="section">
	<div class="box">
		<form id="form" action="<?= $save; ?>" method="post" enctype="multipart/form-data">
			<div class="heading">
				<div class="breadcrumbs">
					<?= $is_ajax ? '' : breadcrumbs(); ?>
				</div>

				<h1><img src="<?= theme_url('image/module.png'); ?>" alt=""/> {{New Block}}</h1>

				<div class="buttons">
					<button class="button">{{Create Block}}</button>
					<a href="<?= $cancel; ?>" class="button">{{Cancel}}</a>
				</div>
			</div>
			<div class="section">

				<table class="form">
					<tr>
						<td class="required">{{Block Name}}</td>
						<td><input type="text" name="name" value="<?= $name; ?>"/></td>
					</tr>
					<tr>
						<td class="required">{{Block Path}}</td>
						<td>
							<input type="text" name="path" placeholder="widget/myblock" value="<?= $path; ?>"/><br/>
							<span class="help">{{Relative to the Block Root Directory (app/controller/block/) for example: widget/myblock}}</span>
						</td>
					</tr>
					<tr>
						<td colspan="2"><h2>{{Admin Block Settings}}</h2></td>
					</tr>
					<tr>
						<td>{{Include _profile.tpl?}}</td>
						<td><input type="checkbox" name="profiles_file" value="1" <?= $profiles_file ? 'checked' : ''; ?> />
						</td>
					</tr>
					<tr>
						<td>{{Include _settings.tpl?}}</td>
						<td><input type="checkbox" name="settings_file" value="1" <?= $settings_file ? 'checked' : ''; ?> />
						</td>
					</tr>
					<tr>
						<td colspan="2"><h2>{{Store Front}}</h2></td>
					</tr>
					<tr>
						<td>{{Theme}}</td>
						<td>
							<?= build(array(
								'type'   => 'multiselect',
								'name'   => 'themes',
								'data'   => $data_themes,
								'select' => $themes,
								'value'  => 'name',
								'label'  => 'name',
							)); ?>
						</td>
					</tr>
				</table>
			</div>
		</form>
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



<?= $is_ajax ? '' : call('admin/footer'); ?>
