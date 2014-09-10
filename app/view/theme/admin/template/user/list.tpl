<?= call('admin/common/header'); ?>
	<div class="section">
		<?= breadcrumbs(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("Users"); ?></h1>

				<? if (user_can('modify', 'user')) { ?>
					<div class="batch_actions">
						<?= block('widget/batch_action', null, $batch_action); ?>
					</div>
					<div class="buttons">
						<a href="<?= $insert; ?>" class="button"><?= _l("Insert"); ?></a>
					</div>
				<? } ?>
			</div>
			<div class="section">
				<?= $listing; ?>
			</div>
		</div>
	</div>

<?= call('admin/common/footer'); ?>