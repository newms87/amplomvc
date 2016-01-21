<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<? if (user_can('w', 'admin/plugin/find')) { ?>
					<a href="<?= site_url('admin/plugin/find'); ?>" class="button">{{Find A Plugin}}</a>
				<? } ?>
			</div>
		</div>
		<div class="section row">
			<?= block('widget/views', null, array(
				'path'  => 'admin/plugin/listing',
				'group' => 'Plugins',
			)); ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).click(function(e) {
		var $target = $(e.target);

		if ($target.is('.action-uninstall')) {
			$.ampConfirm({
				title:     '{{Uninstall Plugin?}}',
				text:      '{{Are you sure you want to uninstall this plugin?}}',
				onConfirm: function() {
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
						onAction: function($modal, action) {
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
