<?= IS_AJAX ? '' : call('admin/common/header'); ?>
<div class="section">
	<?= breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/settings/system-update.png'); ?>" alt=""/> <?= _l("System Update"); ?></h1>

			<div class="buttons">
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>
		<div class="section">
			<form id="form_version" action="<?= $action; ?>" method="post">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Update system to Version:"); ?></td>
						<td><?=
							build('select', array(
								'name'   => 'version',
								'data'   => $data_versions,
								'select' => $version
							)); ?></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" class="button" value="<?= _l("Update Version"); ?>"/></td>
					</tr>
				</table>
			</form>
			<form id="form_auto_update" action="<?= $action; ?>" method="post">
				<table class="form">
					<tr>
						<td> <?= _l("Activate / Deactivate System Automatic Updates"); ?></td>
						<td>
							<? if ($auto_update) { ?>
								<input type="hidden" name="auto_update" value="0"/>
								<input type="submit" class="button" value="<?= _l("Deactivate Automatic Updates"); ?>"/>
							<? } else { ?>
								<input type="hidden" name="auto_update" value="1"/>
								<input type="submit" class="button" value="<?= _l("Activate Automatic Updates"); ?>"/>
							<? } ?>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= IS_AJAX ? '' : call('admin/common/footer'); ?>
