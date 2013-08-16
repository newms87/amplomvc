<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>

	<div id="content">
		<?= $this->breadcrumb->render(); ?>
		<?= $content_top; ?>

		<?= $content; ?>

		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>