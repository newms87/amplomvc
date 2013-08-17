<?= $header; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<? if ($error_warning) { ?>
			<div class="message_box warning"><?= $error_warning; ?></div>
		<? } ?>
		<? if ($success) { ?>
			<div class="message_box success"><?= $success; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'backup.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons"><a onclick="$('#restore').submit();" class="button"><?= $button_restore; ?></a><a
						onclick="$('#backup').submit();" class="button"><?= $button_backup; ?></a></div>
			</div>
			<div class="content">
				<form action="<?= $restore; ?>" method="post" enctype="multipart/form-data" id="restore">
					<table class="form">
						<tr>
							<td><?= $entry_restore; ?></td>
							<td><input type="file" name="import"/></td>
						</tr>
					</table>
				</form>
				<form action="<?= $backup; ?>" method="post" enctype="multipart/form-data" id="backup">
					<table class="form">
						<tr>
							<td><?= $entry_backup; ?></td>
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
								<a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?= $text_select_all; ?></a>
								/ <a
									onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?= $text_unselect_all; ?></a>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
<?= $footer; ?>