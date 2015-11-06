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
				<?= block('widget/views', null, array(
					'path'  => 'admin/plugin/listing',
					'group' => 'Plugins',
				)); ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).click(function (e) {
		var $target = $(e.target);

		if ($target.is('.action-uninstall')) {
			$.ampConfirm({
				title:     '{{Uninstall Plugin?}}',
				text:      '{{Are you sure you want to uninstall this plugin?}}',
				onConfirm: function () {
					$.ampConfirm({
						title:    "{{Keep Data?}}",
						text:     "{{Do you want to keep the data associated with this plugin?}}",
						buttons:  {
							confirm: {
								label: 'Keep Data'
							},
							cancel:  {
								label: 'Remove Data',
							}
						},
						onAction: function (action) {
							location = $target.attr('href') + (action === 'confirm' ? '&keep_data=1' : '');
						}
					});
				}
			})
			return false;
		}
	});
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
