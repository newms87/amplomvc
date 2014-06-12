<?= call('admin/common/header'); ?>

<div id="theme-settings" class="section">
	<?= breadcrumbs(); ?>
	<form action="<?= $save; ?>" method="post" class="box">
		<div class="heading">
			<h1>
				<img class="icon" src="<?= theme_url('image/settings/theme.png'); ?>" alt=""/> <?= _l("Theme Settings"); ?></h1>

			<div class="buttons">
				<button class="button"><?= _l("Save"); ?></button>
				<a href="<?= site_url('admin/setting/store'); ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>

		<div class="section select-store row">
			<h2><?= _l("Choose Store"); ?></h2>

			<div class="store-list">
				<a class="store <?= empty($store['store_id']) ? 'active' : ''; ?>" href="<?= site_url('admin/setting/theme'); ?>"><?= _l("All Stores"); ?></a>

				<? foreach ($data_stores as $s) { ?>
					<a class="store <?= $s['store_id'] == $store['store_id'] ? 'active' : ''; ?>" href="<?= site_url('admin/setting/theme', 'store_id=' . $s['store_id']); ?>"><?= $s['name']; ?></a>
				<? } ?>
			</div>

			<div class="col xs-12 md-6 theme-settings">
				<h3><?= _l("Modify Theme Settings for %s", $store['name']); ?></h3>

				<div class="theme-setting-list">
					<? foreach ($configs as $key => $config) { ?>

						<label for="config-<?= $key; ?>" class="theme-setting">
							<div class="cell">
								<div class="key"><?= $config['key']; ?></div>
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
				<h3><?= _l("Custom Styles"); ?></h3>
				<div class="code-editor">
					<textarea id="stylesheet-editor" name="stylesheet" class=""><?= file_get_contents($stylesheet); ?></textarea>
				</div>
			</div>
		</div>

	</form>
</div>

<script type="text/javascript">
	$('#stylesheet-editor').codemirror({mode: 'sass'});

	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= call('admin/common/footer'); ?>
