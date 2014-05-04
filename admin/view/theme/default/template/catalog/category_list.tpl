<?= _call('common/header'); ?>

<div class="section">
	<?= _breadcrumbs(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("Category"); ?></h1>

			<? if ($can_modify) { ?>
				<div class="batch_actions">
					<?= _block('widget/batch_action', null, $batch_action); ?>
				</div>
				<div class="buttons">
					<a href="<?= $insert; ?>" class="button"><?= _l("Insert"); ?></a>
					<a onclick="do_batch_action('copy')" class="button"><?= _l("Copy"); ?></a>
				</div>
			<? } ?>
		</div>
		<div class="section">
			<?= $listing; ?>
		</div>
	</div>
</div>

<?= _call('common/footer'); ?>
