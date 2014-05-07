<?= call('common/header'); ?>
<div class="section">
	<?= breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("Settings"); ?></h1>

			<div class="buttons">
				<a href="<?= $insert; ?>" class="button"><?= _l("Insert"); ?></a>
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
</div>
<?= call('common/footer'); ?>
