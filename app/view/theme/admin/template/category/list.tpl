<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Categories}}</h1>

			<? if (user_can('w', 'admin/category/batch_action')) { ?>
				<div class="batch_actions">
					<?= block('widget/batch_action', null, $batch_action); ?>
				</div>
			<? } ?>

			<? if (user_can('r', 'admin/category/form')) { ?>
				<div class="buttons">
					<a href="<?= site_url('admin/category/form'); ?>" class="button">{{New Category}}</a>
				</div>
			<? } ?>
		</div>
		<div class="section">
			<?= block('widget/views', null, array(
				'path'  => 'admin/category/listing',
				'group' => 'Category List',
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
