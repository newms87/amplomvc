<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'payment.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td><?= $entry_total; ?></td>
						<td><input type="text" name="cod_total" value="<?= $cod_total; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_order_status; ?></td>
						<td>
							<?= $this->builder->set_config(false, 'title'); ?>
							<?= $this->builder->build('select', $data_order_statuses, 'cod_order_status_id', $cod_order_status_id); ?>
						</td>
					</tr>
					<tr>
						<td><?= $entry_geo_zone; ?></td>
						<td>
							<?= $this->builder->set_config('geo_zone_id', 'name'); ?>
							<?= $this->builder->build('select', $data_geo_zones, 'cod_geo_zone_id', $cod_geo_zone_id); ?>
						</td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td>
							<?= $this->builder->build('select', $data_statuses, 'cod_status', $cod_status); ?>
						</td>
					</tr>
					<tr>
						<td><?= $entry_sort_order; ?></td>
						<td><input type="text" name="cod_sort_order" value="<?= $cod_sort_order; ?>" size="1" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('errors', $errors); ?>
<?= $footer; ?> 