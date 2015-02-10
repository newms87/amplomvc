<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= $page_title; ?></h1>

			<div class="buttons">
				<a href="<?= $insert; ?>" class="button">{{Insert}}</a>
			</div>
		</div>
		<div class="section">
			<?= $listing; ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
