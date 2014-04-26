<?= _call('common/header'); ?>
<div class="section">
	<?= _breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= _l("Navigation"); ?></h1>

			<div class="batch_actions">
				<?= $this->builder->batchAction('#listing [name="selected[]"]', $batch_actions, $batch_update); ?>
			</div>
			<div class="buttons">
				<a onclick="location = '<?= $insert; ?>'" class="button"><?= _l("Insert"); ?></a>
				<a onclick="do_batch_action('copy')" class="button"><?= _l("Copy"); ?></a>
			</div>
		</div>
		<div class="section">
			<div id="listing">
				<?= $list_view; ?>
			</div>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('.actions a.reset').click(function () {
		return confirm("<?= _l("This will reset the Admin Navigation menu to the Default Menu. You will lose all changes made by Plugins and User entries. Are you sure you want to continue?"); ?>");
	});
</script>
<?= _call('common/footer'); ?>
