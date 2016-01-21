<?= $is_ajax ? '' : call('admin/header'); ?>

<div id="theme-settings" class="section">
	<form action="<?= site_url('admin/settings/theme/save'); ?>" method="post" class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<button>{{Save}}</button>
			</div>
		</div>

		<div class="section select-store row">
			<div class="col xs-12 md-6 top theme-settings">
				<a class="button" href="<?= site_url('admin/settings/theme/restore_defaults'); ?>">{{Restore Defaults}}</a>

				<div class="theme-setting-list">
					<? foreach ($configs as $key => $config) { ?>
						<? if ($config['type'] === 'section') { ?>
							<h4><?= $config['title']; ?></h4>
							<? continue;
						} ?>

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


</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
