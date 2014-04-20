<?= $this->call('common/header'); ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<? if ($error_warning) { ?>
			<div class="message warning"><?= $error_warning; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= URL_THEME_IMAGE . 'total.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
						href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
			</div>
			<div class="section">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td><?= _l("Status"); ?></td>
							<td><select name="reward_status">
									<? if ($reward_status) { ?>
										<option value="1" selected="selected"><?= _l("Enabled"); ?></option>
										<option value="0"><?= _l("Disabled"); ?></option>
									<? } else { ?>
										<option value="1"><?= _l("Enabled"); ?></option>
										<option value="0" selected="selected"><?= _l("Disabled"); ?></option>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= _l("Sort Order"); ?></td>
							<td><input type="text" name="reward_sort_order" value="<?= $reward_sort_order; ?>" size="1"/></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
<?= $this->call('common/footer'); ?>
