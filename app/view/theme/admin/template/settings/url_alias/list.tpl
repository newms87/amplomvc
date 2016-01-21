<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<? if (user_can('w', 'admin/settings/url_alias/form')) { ?>
					<a href="<?= site_url('admin/settings/url-alias/form'); ?>" class="button">{{Add Alias}}</a>
				<? } ?>
			</div>
		</div>

		<div class="section row">
			<? if (!empty($batch_action) && user_can('w', 'admin/settings/url_alias/batch_action')) { ?>
				<div class="batch-action row right padding-bottom">
					<?= block('widget/batch_action', null, $batch_action); ?>
				</div>
			<? } ?>

			<?= block('widget/views', null, array(
				'group' => 'url_alias_list',
				'path'  => 'admin/settings/url_alias/listing',
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
