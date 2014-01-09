<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $content_top; ?>

		<h1><?= _l("The page you requested cannot be found!"); ?></h1>

		<div class="section"><?= _l("The page you requested cannot be found."); ?></div>
		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
		</div>

		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>