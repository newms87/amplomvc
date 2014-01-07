<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'backup.png'; ?>" alt=""/> <?= _l("Development Console"); ?></h1>

				<div class="buttons">
					<a href="<?= $return; ?>" class="button"><?= _l("Back To Dashboard"); ?></a>
				</div>
			</div>
			<div class="section">
				<a class="dev_console_item" href="<?= $url_site_management; ?>">
					<div class="title"><?= _l("Site Management"); ?></div>
					<div class="image"><img src="<?= HTTP_THEME_IMAGE . 'dev/site_management.png'; ?>"/></div>
				</a>
				<a class="dev_console_item" href="<?= $url_sync; ?>">
					<div class="title"><?= _l("Synchronize Sites"); ?></div>
					<div class="image"><img src="<?= HTTP_THEME_IMAGE . 'dev/sync.png'; ?>"/></div>
				</a>
				<a class="dev_console_item" href="<?= $url_backup_restore; ?>">
					<div class="title"><?= _l("Site Backup & Restore"); ?></div>
					<div class="image"><img src="<?= HTTP_THEME_IMAGE . 'dev/restore.png'; ?>"/></div>
				</a>
				<a class="dev_console_item" href="<?= $url_db_admin; ?>">
					<div class="title"><?= _l("DB Admin"); ?></div>
					<div class="image"><img src="<?= HTTP_THEME_IMAGE . 'dev/db_admin.png'; ?>"/></div>
				</a>
			</div>
		</div>
	</div>

<?= $footer; ?>