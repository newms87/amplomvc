<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{Navigation}}</h1>

			<div class="batch_actions">
				<?= block('widget/batch_action', null, $batch_action); ?>
			</div>
			<div class="buttons">
				<a href="<?= site_url('admin/navigation/form'); ?>" class="button">{{Insert}}</a>
				<a onclick="do_batch_action('copy')" class="button">{{Copy}}</a>
			</div>
		</div>
		<div class="section">
			<?= $listing; ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
