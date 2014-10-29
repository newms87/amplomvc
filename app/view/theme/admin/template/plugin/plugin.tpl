<?= IS_AJAX ? '' : call('admin/header'); ?>
<div class="section">
	<?= IS_AJAX ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("Plugins"); ?></h1>
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

<script type="text/javascript">
	$('.action-uninstall').click(function () {
		keep_data = 0;

		if (confirm("<?= _l("Do you want to keep the data associated with this plugin?"); ?>")) {
			keep_data = 1;
		}

		$(this).attr('href', $(this).attr('href') + '&keep_data=' + keep_data);
	});
</script>

<?= IS_AJAX ? '' : call('admin/footer'); ?>
