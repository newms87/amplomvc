<?= $this->call('common/header'); ?>

<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'module.png'; ?>" alt=""/> <?= $page_title; ?></h1>

			<div class="buttons">
				<a onclick="$('#form').trigger('saving').submit();" class="button"><?= _l("Save"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="extension_settings">
					<table class="form">
						<tr>
							<td><?= _l("Display Title"); ?></td>
							<td><input type="text" name="title" value="<?= $title; ?>"/></td>
						</tr>
						<? if (!empty($extend_settings)) { ?>
							<tr>
								<td colspan="2"><?= $extend_settings; ?></td>
							</tr>
						<? } ?>
						<tr>
							<td><?= _l("Order Complete Status"); ?></td>
							<td>
								<? $this->builder->setConfig(false, 'title'); ?>
								<?= $this->builder->build('select', $data_order_statuses, "settings[complete_order_status_id]", $settings['complete_order_status_id']); ?>
							</td>
						</tr>
						<tr>
							<td>
								<div><?= _l("Order Total:"); ?></div>
								<span class="help"><?= _l("The minimum order total before this payment method is avaialable."); ?></span>
							</td>
							<td><input type="text" name="settings[min_total]" value="<?= $settings['min_total']; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Accepted Geo Zone"); ?></td>
							<? $this->builder->setConfig('geo_zone_id', 'name'); ?>
							<td><?= $this->builder->build('select', $data_geo_zones, "settings[geo_zone_id]", $settings['geo_zone_id']); ?></td>
						</tr>
						<tr>
							<td><?= _l("Sort Order"); ?></td>
							<td><input type="text" name="sort_order" value="<?= $sort_order; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Status"); ?></td>
							<td><?= $this->builder->build('select', $data_statuses, "status", $status); ?></td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('errors', $errors); ?>

<?= $this->call('common/footer'); ?>
