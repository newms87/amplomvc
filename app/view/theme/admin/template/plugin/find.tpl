<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Find A Plugin}}</h1>
		</div>

		<div class="section">
			<div class="plugin-list">
				<? html_dump($plugins, 'plugins'); ?>
				<? foreach ($plugins as $plugin) { ?>
					<div class="plugin">
						<a href="<?= site_url('admin/plugin/download', 'name=' . $plugin['full_name']); ?>">
							<h2><?= $plugin['name']; ?></h2>
						</a>
						<p class="description"><?= $plugin['description']; ?></p>
					</div>
				<? } ?>
			</div>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
