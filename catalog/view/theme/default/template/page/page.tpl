<?= $common_header; ?>
<?= $area_left; ?><?= $area_right; ?>

<div class="content">
	<?= $this->breadcrumb->render(); ?>

	<style><?= $css; ?></style>

	<div class="section">
		<? if (!empty($display_title)) { ?>
			<h1><?= $title; ?></h1>
		<? } ?>

		<?= $area_top; ?>

		<div class="page_content"><?= $content; ?></div>
	</div>

	<?= $area_bottom; ?>
</div>

<?= $common_footer; ?>
