<?= call('common/header'); ?>
<div class="section">
	<?= breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("Navigation"); ?></h1>

			<div class="batch_actions">
				<?= block('widget/batch_action', null, $batch_action); ?>
			</div>
			<div class="buttons">
				<a href="<?= $insert; ?>" class="button"><?= _l("Insert"); ?></a>
				<a onclick="do_batch_action('copy')" class="button"><?= _l("Copy"); ?></a>
			</div>
		</div>
		<div class="section">
			<?= $listing; ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('.actions a.reset').click(function () {
		return confirm("<?= _l("This will reset the Admin Navigation menu to the Default Menu. You will lose all changes made by Plugins and User entries. Are you sure you want to continue?"); ?>");
	});
</script>

<?= call('common/footer'); ?>
