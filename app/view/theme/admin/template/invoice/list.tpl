<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<? if (user_can('w', 'admin/invoice/form')) { ?>
					<a href="<?= site_url('admin/invoice/form'); ?>" class="button">{{Add Invoice}}</a>
				<? } ?>
			</div>
		</div>

		<div class="section row">
			<? if (user_can('w', 'admin/invoice/batch_action')) { ?>
				<div class="batch-action row right padding-bottom">
					<?= block('widget/batch_action', null, $batch_action); ?>
				</div>
			<? } ?>

			<?=
			block('widget/views', null, array(
				'path'  => 'admin/invoice/listing',
				'group' => 'Invoices',
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
