<?= _call('common/header'); ?>

<div class="section">
	<?= _breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/setting.png'); ?>" alt=""/>
				<?= $page_title . _l("Extension File:"); ?>
				<span class="extension_file_name"><?= $edit_file; ?></span>
			</h1>

			<div class="buttons">
				<a onclick="$('#extension_editor').submit()" class="button"><?= _l("Save"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>
		<div class="section">
			<form id="extension_editor" action="<?= $save; ?>" method="post">
				<textarea id="extension_editor_textarea" name="contents"><?= $contents; ?></textarea>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#extension_editor_textarea').codemirror({mode: 'php'});
</script>

<?= _call('common/footer'); ?>
