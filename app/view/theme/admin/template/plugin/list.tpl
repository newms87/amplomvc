<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Plugins}}</h1>

			<div class="buttons">
				<a href="<?= site_url('admin/plugin/find'); ?>" class="button">{{Find A Plugin}}</a>
			</div>
		</div>
		<div class="section">
			<div id="listing">
				<?= $listing; ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('.action-uninstall').click(function () {
		keep_data = 0;

		if (confirm("{{Do you want to keep the data associated with this plugin?}}")) {
			keep_data = 1;
		}

		$(this).attr('href', $(this).attr('href') + '&keep_data=' + keep_data);
	});
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>