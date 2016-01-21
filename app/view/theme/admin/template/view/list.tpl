<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<? if (user_can('w', 'admin/view/form')) { ?>
					<a class="button" href="<?= site_url('admin/view/form'); ?>">{{Create View}}</a>
				<? } ?>
			</div>
		</div>

		<div class="section row">
			<?= block('widget/views', null, array(
				'group'        => 'views',
				'path'         => 'admin/view/listing',
				'view_listing' => 'view_listings'
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
