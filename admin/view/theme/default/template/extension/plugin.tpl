<?= $header; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= $head_title; ?></h1>
			</div>
			<div class="content">
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

<?= $footer; ?>