<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'backup.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<a href="<?= $return; ?>" class="button"><?= $button_return; ?></a>
			</div>
		</div>
		<div class="content">
			<a class="dev_console_item" href="<?= $url_site_management; ?>">
				<div class="title"><?= $console_site_management; ?></div>
				<div class="image"><img src="<?= HTTP_THEME_IMAGE . 'dev/site_management.png'; ?>" /></div>
			</a>
			<a class="dev_console_item" href="<?= $url_sync; ?>">
				<div class="title"><?= $console_sync; ?></div>
				<div class="image"><img src="<?= HTTP_THEME_IMAGE . 'dev/sync.png'; ?>" /></div>
			</a>
			<a class="dev_console_item" href="<?= $url_backup_restore; ?>">
				<div class="title"><?= $console_backup_restore; ?></div>
				<div class="image"><img src="<?= HTTP_THEME_IMAGE . 'dev/restore.png'; ?>" /></div>
			</a>
		</div>
	</div>
</div>

<?= $footer; ?>