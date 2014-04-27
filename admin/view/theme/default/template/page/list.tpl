<?= _call('common/header'); ?>

<div class="section">
	<?= _breadcrumbs(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= _l("Page"); ?></h1>

			<div class="batch_actions">
				<?= _block('widget/batch_action', null, $batch_action); ?>
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

<?= _call('common/footer'); ?>
