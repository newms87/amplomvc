<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<? if ($error_warning) { ?>
			<div class="message_box warning"><?= $error_warning; ?></div>
		<? } ?>
		<? if ($success) { ?>
			<div class="message_box success"><?= $success; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'backup.png'; ?>" alt=""/> <?= _l("Backup / Restore"); ?></h1>

				<div class="buttons"><a onclick="$('#restore').submit();" class="button"><?= _l("Restore"); ?></a><a onclick="$('#backup').submit();" class="button"><?= _l("Backup"); ?></a></div>
			</div>
			<div class="section">
				<form action="<?= $restore; ?>" method="post" enctype="multipart/form-data" id="restore">
					<table class="form">
						<tr>
							<td><?= _l("Restore Backup:"); ?></td>
							<td><input type="file" name="import"/></td>
						</tr>
					</table>
				</form>
				<form action="<?= $backup; ?>" method="post" enctype="multipart/form-data" id="backup">
					<table class="form">
						<tr>
							<td><?= _l("Backup:"); ?></td>
							<td>
								<div class="scrollbox" style="margin-bottom: 5px;">
									<? $class = 'odd'; ?>
									<? foreach ($tables as $table) { ?>
										<? $class = ($class == 'even' ? 'odd' : 'even'); ?>
										<div class="<?= $class; ?>">
											<input type="checkbox" name="backup[]" value="<?= $table; ?>" checked="checked"/>
											<?= $table; ?></div>
									<? } ?>
								</div>
								<a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?= _l("Select All"); ?></a>
								/ <a onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?= _l("Unselect All"); ?></a>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
<?= $footer; ?>