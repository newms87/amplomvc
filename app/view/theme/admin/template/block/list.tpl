<?= call('admin/common/header'); ?>
	<div class="section">
		<?= breadcrumbs(); ?>
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

<?= call('admin/common/footer'); ?>
