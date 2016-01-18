<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Layouts}}</h1>

			<? if (user_can('w', 'admin/layout/save')) { ?>
				<div class="batch-action row right padding-bottom">
					<?= block('widget/batch_action', null, $batch_action); ?>
				</div>
				<div class="buttons">
					<a href="<?= site_url('admin/layout/form'); ?>" class="button">{{Add Layout}}</a>
				</div>
			<? } ?>
		</div>
		<div class="section">
			<?= $listing; ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
