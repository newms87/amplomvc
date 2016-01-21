<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<? if (user_can('w', 'admin/settings/clear-cache')) { ?>
					<a href="<?= site_url('admin/settings/clear-cache'); ?>" class="button">{{Clear Cache}}</a>
				<? } ?>
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
		</div>
	</div>
</div>
<?= $is_ajax ? '' : call('admin/footer'); ?>
