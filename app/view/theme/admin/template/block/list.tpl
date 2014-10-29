<?= IS_AJAX ? '' : call('admin/header'); ?>
	<div class="section">
		<?= IS_AJAX ? '' : breadcrumbs(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= $page_title; ?></h1>

				<div class="buttons">
					<a href="<?= $insert; ?>" class="button"><?= _l("Insert"); ?></a>
				</div>
			</div>
			<div class="section">
				<?= $listing; ?>
			</div>
		</div>
	</div>

<?= IS_AJAX ? '' : call('admin/footer'); ?>
