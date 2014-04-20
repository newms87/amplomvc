<?= $this->call('common/header'); ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= URL_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= _l("System Update"); ?></h1>

				<div class="buttons">
					<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
				</div>
			</div>
			<div class="section">
				<form id="form_version" action="<?= $action; ?>" method="post">
					<table class="form">
						<tr>
							<td class="required"> <?= _l("Update system to Version:"); ?></td>
							<td><?= $this->builder->build('select', $data_versions, 'version', $version); ?></td>
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

<?= $this->builder->js('errors', $errors); ?>

<?= $this->call('common/footer'); ?>
