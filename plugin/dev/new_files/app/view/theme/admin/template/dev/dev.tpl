<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>
		</div>
		<div class="section">
			<a class="dev_console_item" href="<?= $url_site_management; ?>">
				<div class="title">{{Site Management}}</div>
				<div class="image"><img src="<?= theme_url('image/dev/sync.png') ?>"/></div>
			</a>
			<a class="dev_console_item" href="<?= $url_sync; ?>">
				<div class="title">{{Synchronize Sites}}</div>
				<div class="image"><img src="<?= theme_url('image/dev/restore.png'); ?>"/></div>
			</a>
			<a class="dev_console_item" href="<?= $url_backup_restore; ?>">
				<div class="title">{{Site Backup & Restore}}</div>
				<div class="image"><img src="<?= theme_url('image/dev/db_admin.png'); ?>"/></div>
			</a>
			<a class="dev_console_item" href="<?= $url_db_admin; ?>">
				<div class="title">{{DB Admin}}</div>
				<div class="image"><img src="<?= theme_url('image/dev/db_admin.png'); ?>"/></div>
			</a>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
