<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_image('settings/alias.png'); ?>" alt=""/> {{URL Alias}}</h1>

			<? if (user_can('w', 'admin/settings/url_alias')) { ?>
				<? if (!empty($batch_action)) { ?>
					<div class="batch_actions">
						<?= block('widget/batch_action', null, $batch_action); ?>
					</div>
				<? } ?>

				<div class="buttons">
					<a href="<?= site_url('admin/settings/url-alias/form'); ?>" class="button">{{Add Alias}}</a>
				</div>
			<? } ?>
		</div>

		<div class="section">
			<?= block('widget/views', null, array(
				'group' => 'url_alias_list',
				'path'  => 'admin/settings/url_alias/listing',
			)); ?>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
