<?= $header; ?>

	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a onclick="$('#form').trigger('saving').submit();" class="button"><?= $button_save; ?></a>
					<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
				</div>
			</div>
			<div class="section">
				<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
					<div id="extension_settings">
						<table class="form">
							<tr>
								<td><?= $entry_title; ?></td>
								<td><input type="text" name="title" value="<?= $title; ?>" /></td>
							</tr>
							<? if (!empty($extend_settings)) { ?>
								<tr>
									<td colspan="2"><?= $extend_settings; ?></td>
								</tr>
							<? } ?>
							<tr>
								<td><?= $entry_complete_status; ?></td>
								<td>
									<? $this->builder->setConfig(false, 'title'); ?>
									<?= $this->builder->build('select', $data_order_statuses, "settings[complete_order_status_id]", $settings['complete_order_status_id']); ?>
								</td>
							</tr>
							<tr>
								<td><?= $entry_min_total; ?></td>
								<td><input type="text" name="settings[min_total]" value="<?= $settings['min_total']; ?>" /></td>
							</tr>
							<tr>
								<td><?= $entry_geo_zone; ?></td>
								<? $this->builder->setConfig('geo_zone_id', 'name'); ?>
								<td><?= $this->builder->build('select', $data_geo_zones, "settings[geo_zone_id]", $settings['geo_zone_id']); ?></td>
							</tr>
							<tr>
								<td><?= $entry_sort_order; ?></td>
								<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" /></td>
							</tr>
							<tr>
								<td><?= $entry_status; ?></td>
								<td><?= $this->builder->build('select', $data_statuses, "status", $status); ?></td>
							</tr>
						</table>
					</div>
				</form>
			</div>
		</div>
	</div>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
