<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a href="<?= $insert; ?>" class="button"><?= $button_insert; ?></a>
				</div>
			</div>
			<div class="section">
				<div class="menu_icons">
					<a class="menu_item" href="<?= $admin_settings; ?>">
						<div class="title"><?= $button_admin_settings; ?></div>
						<div class="image"><img src="<?= HTTP_THEME_IMAGE . "admin_settings.png"; ?>"/></div>
					</a>
					<a class="menu_item" href="<?= $system_update; ?>">
						<div class="title"><?= $button_system_update; ?></div>
						<div class="image"><img src="<?= HTTP_THEME_IMAGE . "system_update.png"; ?>"/></div>
					</a>
				</div>
				<div class="section">
					<div class="limits">
						<?= $limits; ?>
					</div>

					<div id="listing">
						<?= $list_view; ?>
					</div>
					<div class="pagination"><?= $pagination; ?></div>
				</div>
			</div>
		</div>
	</div>
<?= $footer; ?>