<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>

	<div class="content">
		<?= $this->breadcrumb->render(); ?>

		<? if ($display_title) { ?>
			<h1><?= $title; ?></h1>
		<? } ?>

		<?= $content_top; ?>

		<?= $content; ?>

		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>