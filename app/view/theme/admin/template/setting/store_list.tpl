<?= IS_AJAX ? '' : call('admin/common/header'); ?>
<div class="section">
	<?= IS_AJAX ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("Stores & Settings"); ?></h1>

			<div class="buttons">
				<a href="<?= $insert; ?>" class="button"><?= _l("Add Store"); ?></a>
				<a href="<?= site_url('admin'); ?>" class="button"><?= _l("Back"); ?></a>
			</div>
		</div>
		<div class="section">
			<div class="menu_icons clearfix">
				<? foreach ($widgets as $widget) { ?>
					<a class="menu_item" href="<?= $widget['url']; ?>">
						<div class="title"><?= $widget['title']; ?></div>
						<div class="image"><img src="<?= $widget['icon']; ?>"/></div>
					</a>
				<? } ?>
			</div>

			<div class="section">
				<?= block('widget/views', null, array('path' => 'admin/setting/store/listing')); ?>
			</div>
		</div>
	</div>
</div>
<?= IS_AJAX ? '' : call('admin/common/footer'); ?>
