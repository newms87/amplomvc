<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<form id="form_version" action="<?= $action; ?>" method="post">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_version; ?></td>
						<td><?= $this->builder->build('select', $data_versions, 'version', $version); ?></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" class="button" value="<?= $button_version; ?>" /></td>
					</tr>
				</table>
			</form>
			<form id="form_auto_update" action="<?= $action; ?>" method="post">
				<table class="form">
					<tr>
						<td> <?= $entry_auto_update; ?></td>
						<td>
							<? if ($auto_update) { ?>
								<input type="hidden" name="auto_update" value="0" />
								<input type="submit" class="button" value="<?= $button_auto_update_deactivate; ?>" />
							<? } else { ?>
								<input type="hidden" name="auto_update" value="1" />
								<input type="submit" class="button" value="<?= $button_auto_update_activate; ?>" />
							<? } ?>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('errors',$errors); ?>

<?= $footer; ?>