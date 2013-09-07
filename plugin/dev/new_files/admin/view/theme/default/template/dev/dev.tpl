<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'backup.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a href="<?= $return; ?>" class="button"><?= $button_return; ?></a>
				</div>
			</div>
			<div class="section">
				<a class="dev_console_item" href="<?= $url_site_management; ?>">
					<div class="title"><?= $console_site_management; ?></div>
					<div class="image"><img src="<?= HTTP_THEME_IMAGE . 'dev/site_management.png'; ?>"/></div>
				</a>
				<a class="dev_console_item" href="<?= $url_sync; ?>">
					<div class="title"><?= $console_sync; ?></div>
					<div class="image"><img src="<?= HTTP_THEME_IMAGE . 'dev/sync.png'; ?>"/></div>
				</a>
				<a class="dev_console_item" href="<?= $url_backup_restore; ?>">
					<div class="title"><?= $console_backup_restore; ?></div>
					<div class="image"><img src="<?= HTTP_THEME_IMAGE . 'dev/restore.png'; ?>"/></div>
				</a>
				<a class="dev_console_item" href="<?= $url_db_admin; ?>">
					<div class="title"><?= $console_db_admin; ?></div>
					<div class="image"><img src="<?= HTTP_THEME_IMAGE . 'dev/db_admin.png'; ?>"/></div>
				</a>
			</div>
		</div>
	</div>

<?= $footer; ?>