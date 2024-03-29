<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>
		</div>
		<div class="section">
			<form id="site_backup_restore" action="" method="post">
				<table class="form">
					<tr>
						<td>
							<label>{{Backup}}</label>
							<input type="submit" class="button" name="site_backup" value="{{Backup}}"/>
							<br/><br/>
							<input type="submit" class="button" name="sync_file" value="{{Sync File}}"/>
						</td>
						<td><?= build(array(
								'type'   => 'multiselect',
								'name'   => 'tables',
								'data'   => $data_tables,
								'select' => $tables
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Restore}}</td>
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
							<input type="submit" class="button" name="site_restore" value="{{Restore}}"/>
							<input type="submit" class="button" onclick="$(this).closest('form').attr('target', '_blank');" name="backup_download" value="{{Download}}"/>
						</td>
					</tr>
				</table>
			</form>
			<form action="" method="post" enctype="multipart/form-data">
				<table class="form">
					<tr>
						<td>
							<label>{{Execute File}}</label>
							<input type="submit" class="button" name="execute_file" value="{{Execute File}}"/>
							<br/><br/>
							<input type="submit" class="button" name="execute_sync_file" value="{{Execute Sync File}}"/>
						</td>
						<td>
							<input type="file" name="filename" value=""/>
						</td>
					</tr>
				</table>
			</form>

			<div class="default-install">
				<a id="overwrite_default_db" class="button" href="<?= site_url('admin/dev/default-install'); ?>">{{Overwrite Default Installation DB File}}</a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#overwrite_default_db').click(function () {
		return confirm("This will overwrite the Default Database Installation for Amplo MVC! Are you sure you want to continue?");
	});
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
