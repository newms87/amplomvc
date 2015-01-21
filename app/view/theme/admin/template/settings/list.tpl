<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Settings}}</h1>

			<div class="buttons">
				<a href="<?= site_url('admin'); ?>" class="button">{{Admin Home}}</a>
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
