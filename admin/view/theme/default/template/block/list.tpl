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
				<div id="listing">
					<?= $list_view; ?>
				</div>
				<div class="pagination"><?= $pagination; ?></div>
			</div>
		</div>
	</div>

<?= $footer; ?>