<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section plugin-settings">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Find A Plugin}}</h1>
		</div>

		<div class="section">
			<? html_dump($plugins, 'plugins'); ?>

			<div class="plugin-list col xs-12">
				<? foreach ($plugins as $plugin) { ?>
					<div class="plugin col xs-12 sm-6 md-4 lg-3">
						<a class="name col xs-12" href="<?= site_url('admin/plugin/download', 'name=' . $plugin['full_name']); ?>">
							<h2><?= $plugin['name']; ?></h2>
						</a>
						<p class="description col xs-12 left"><?= $plugin['description']; ?></p>
						<div class="download col xs-12">
							<? if ($plugin['download']) { ?>
								<a href="<?= $plugin['download']; ?>">{{Download Zip}}</a>
							<? } else { ?>
								{{No Download Available}}
							<? } ?>
						</div>
					</div>
				<? } ?>
			</div>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
