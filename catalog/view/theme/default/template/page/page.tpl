<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>

	<div id="content">
		<?= $this->breadcrumb->render(); ?>

		<? if ($display_title) { ?>
			<h1><?= $title; ?></h1>
		<? } ?>

		<?= $content_top; ?>

		<?= $content; ?>

		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>