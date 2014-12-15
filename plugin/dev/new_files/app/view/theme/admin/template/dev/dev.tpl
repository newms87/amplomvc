<?= $is_ajax ? '' : call('admin/header'); ?>
	<div class="section">
		<?= $is_ajax ? '' : breadcrumbs(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= theme_url('image/backup.png'); ?>" alt=""/> {{Development Console}}</h1>

				<div class="buttons">
					<a href="<?= $return; ?>" class="button">{{Back To Dashboard}}</a>
				</div>
			</div>
			<div class="section">
				<a class="dev_console_item" href="<?= $url_site_management; ?>">
					<div class="title">{{Site Management}}</div>
					<div class="image"><img src="<?= theme_url('image/dev/sync.png') ?>"/></div>
				</a>
				<a class="dev_console_item" href="<?= $url_sync; ?>">
					<div class="title">{{Synchronize Sites}}</div>
					<div class="image"><img src="<?= theme_url('image/dev/restore.png'); ?>" /></div>
				</a>
				<a class="dev_console_item" href="<?= $url_backup_restore; ?>">
					<div class="title">{{Site Backup & Restore}}</div>
					<div class="image"><img src="<?= theme_url('image/dev/db_admin.png'); ?>" /></div>
				</a>
				<a class="dev_console_item" href="<?= $url_db_admin; ?>">
					<div class="title">{{DB Admin}}</div>
					<div class="image"><img src="<?= theme_url('image/dev/db_admin.png'); ?>"/></div>
				</a>
			</div>
		</div>
	</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
