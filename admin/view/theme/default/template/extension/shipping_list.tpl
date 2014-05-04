<?= _call('common/header'); ?>
<div class="section">
	<?= _breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("Shipping Extensions"); ?></h1>

			<div class="buttons">
				<a href="<?= $insert; ?>" class="button"><?= _l("Add"); ?></a>
			</div>
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

<?= _call('common/footer'); ?>
