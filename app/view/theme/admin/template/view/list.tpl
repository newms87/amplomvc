<?= IS_AJAX ? '' : call('admin/header'); ?>
	<div class="section">
		<?= IS_AJAX ? '' : breadcrumbs(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("Views"); ?></h1>

				<div class="buttons">
					<a class="button" href="<?= site_url('admin/view/form'); ?>"><?= _l("Create View"); ?></a>
				</div>
			</div>
			<div class="section">
				<?= block('widget/views', null, array('group' => 'views', 'path' => 'admin/view/listing', 'view_listing' => 'view_listings')); ?>
			</div>
		</div>
	</div>

<?= IS_AJAX ? '' : call('admin/footer'); ?>
