<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>

	<div class="content">
		<?= $this->breadcrumb->render(); ?>

		<?= $content_top; ?>

		<style><?= $css; ?></style>

		<div class="section">
			<? if (!empty($display_title)) { ?>
				<h1><?= $title; ?></h1>
			<? } ?>

			<div class="page_content"><?= $content; ?></div>
		</div>

		<?= $content_bottom; ?>
	</div>

<?= $footer; ?>
