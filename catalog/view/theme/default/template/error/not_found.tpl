<?= $common_header; ?>
<?= $area_left; ?><?= $area_right; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $area_top; ?>

	<h1><?= !empty($page_title) ? $page_title : _l("Page Not Found"); ?></h1>

	<div class="section"><?= _l("The page you requested cannot be found."); ?></div>
	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
	</div>

	<?= $area_bottom; ?>
</div>

<?= $common_footer; ?>
