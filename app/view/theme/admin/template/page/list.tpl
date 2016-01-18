<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Pages}}</h1>

			<? if (user_can('w', 'admin/page/batch_action')) { ?>
				<div class="batch-action row right padding-bottom">
					<?= block('widget/batch_action', null, $batch_action); ?>
				</div>
			<? } ?>

			<? if (user_can('w', 'admin/page/form')) { ?>
				<div class="buttons">
					<a href="<?= site_url('admin/page/form'); ?>" class="button">{{Insert}}</a>
				</div>
			<? } ?>
		</div>

		<div class="section">
			<?= block('widget/views', null, array(
				'group' => 'Pages',
				'path'  => 'admin/page/listing',
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
