<?= $is_ajax ? '' : call('admin/header'); ?>

<div id="theme-settings" class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<form action="<?= $save; ?>" method="post" class="box">
		<div class="heading">
			<h1>
				<img class="icon" src="<?= theme_url('image/settings/theme.png'); ?>" alt=""/> {{Theme Settings}}</h1>

			<div class="buttons">
				<button class="button">{{Save}}</button>
				<a href="<?= site_url('admin/setting/store'); ?>" class="button">{{Cancel}}</a>
			</div>
		</div>

		<div class="section select-store row">
			<h2>{{Choose Store}}</h2>

			<div class="store-list">
				<a class="store <?= empty($store['store_id']) ? 'active' : ''; ?>" href="<?= site_url('admin/setting/theme'); ?>">{{All Stores}}</a>

				<? foreach ($data_stores as $s) { ?>
					<a class="store <?= $s['store_id'] == $store['store_id'] ? 'active' : ''; ?>" href="<?= site_url('admin/setting/theme', 'store_id=' . $s['store_id']); ?>"><?= $s['name']; ?></a>
				<? } ?>
			</div>

			<div class="col xs-12 md-6 top theme-settings">
				<a class="button" href="<?= site_url('admin/setting/theme/restore_defaults', 'store_id=' . $store['store_id']); ?>">{{Restore Defaults}}</a>

				<div class="theme-setting-list">
					<? foreach ($configs as $key => $config) { ?>
						<? if ($config['type'] === 'section') { ?>
							<h4><?= $config['title']; ?></h4>
						<? continue; } ?>

						<label for="config-<?= $key; ?>" class="theme-setting">
							<div class="cell">
								<div class="title"><?= $config['title']; ?></div>
								<div class="key">@<?= $key; ?></div>
								<div class="description"><?= $config['description']; ?></div>
								<div class="value">
									<input id="config-<?= $key; ?>" type="text" name="configs[<?= $key; ?>]" value="<?= $config['value']; ?>"/>
								</div>
							</div>
						</label>
					<? } ?>
				</div>
			</div>

			<div class="col xs-12 md-6 theme-stylesheet">
				<h3>{{Custom Styles}}</h3>
				<div class="code-editor">
					<textarea id="stylesheet-editor" name="stylesheet" class=""><?= $stylesheet; ?></textarea>
				</div>
			</div>
		</div>

	</form>
</div>

<script type="text/javascript">
	$('#stylesheet-editor').codemirror({mode: 'sass'});

	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
