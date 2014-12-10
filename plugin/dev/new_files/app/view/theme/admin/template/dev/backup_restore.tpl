<?= IS_AJAX ? '' : call('admin/header'); ?>
<div class="section">
	<?= IS_AJAX ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/backup.png'); ?>" alt=""/> <?= _("Backup / Restore"); ?></h1>

			<div class="buttons">
				<a href="<?= site_url('admin'); ?>" class="button"><?= _l("Return to Dev Console"); ?></a>
			</div>
		</div>
		<div class="section">
			<form id="site_backup_restore" action="" method="post">
				<table class="form">
					<tr>
						<td>
							<label><?= _l("Backup"); ?></label>
							<input type="submit" class="button" name="site_backup" value="<?= _l("Backup"); ?>"/>
							<br/><br/>
							<input type="submit" class="button" name="sync_file" value="<?= _l("Sync File"); ?>"/>
						</td>
						<td><?= build('multiselect', array(
								'name'   => 'tables',
								'data'   => $data_tables,
								'select' => $tables
							)); ?>
						</td>
					</tr>
					<tr>
						<td><?= _l("Restore"); ?></td>
						<td>
							<? foreach ($data_backup_files as $file) { ?>
								<span class="radio-button">
									<input type="radio" name="backup_file" value="<?= $file['path']; ?>" id="radio-button_<?= md5($file['path']); ?>"/>
									<label for="radio-button_<?= md5($file['path']); ?>">
										<span class="date"><?= format('date', $file['date'], 'd M, Y'); ?></span> -
										<span class="name"><?= $file['name']; ?></span> -
										<span class="size"><?= bytes2str($file['size'], 2); ?></span>
									</label>
								</span>
							<? } ?>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type="submit" class="button" name="site_restore" value="<?= _l("Restore"); ?>"/>
							<input type="submit" class="button" onclick="$(this).closest('form').attr('target', '_blank');" name="backup_download" value="<?= _l("Download"); ?>"/>
						</td>
					</tr>
				</table>
			</form>
			<form action="" method="post" enctype="multipart/form-data">
				<table class="form">
					<tr>
						<td>
							<label><?= _l("Execute File"); ?></label>
							<input type="submit" class="button" name="execute_file" value="<?= _l("Execute File"); ?>"/>
							<br/><br/>
							<input type="submit" class="button" name="execute_sync_file" value="<?= _l("Execute Sync File"); ?>"/>
						</td>
						<td>
							<input type="file" name="filename" value=""/>
						</td>
					</tr>
				</table>
			</form>

			<div class="default-install">
				<a id="overwrite_default_db" class="button" href="<?= site_url('admin/dev/default-install'); ?>"><?= _l("Overwrite Default Installation DB File"); ?></a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#overwrite_default_db').click(function () {
		return confirm("This will overwrite the Default Database Installation for Amplo MVC! Are you sure you want to continue?");
	});
</script>

<?= IS_AJAX ? '' : call('admin/footer'); ?>
