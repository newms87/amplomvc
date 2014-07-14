<?= call('admin/common/header'); ?>
	<div class="section">
		<?= breadcrumbs(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("Views"); ?></h1>

				<div class="buttons">
					<a href="<?= site_url('admin/view/form'); ?>"><?= _l("Create View"); ?></a>
				</div>
			</div>
			<div class="section">
				<?= block('widget/views', null, array('group' => 'views', 'view_listing' => 'view_listings')); ?>
			</div>
		</div>
	</div>

<?= call('admin/common/footer'); ?>