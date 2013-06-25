<?= $header; ?>
<div class="content">
	<?= $breadcrumbs; ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'payment.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_email; ?></td>
						<td><input type="text" name="pp_standard_email" size="50" value="<?= $pp_standard_email; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_test; ?></td>
						<td><?= $this->builder->build('radio', $data_yes_no, 'pp_standard_test', $pp_standard_test); ?></td>
					</tr>
					<tr>
						<td><?= $entry_transaction; ?></td>
						<td><?= $this->builder->build('select', $data_auth_sale, 'pp_standard_transaction', $pp_standard_transaction); ?></td>
					</tr>
					<tr>
						<td><?= $entry_pdt_enabled; ?></td>
						<td>
							<?= $this->builder->build('select', $data_statuses, 'pp_standard_pdt_enabled', $pp_standard_pdt_enabled); ?>
							<span class="help"><?= $entry_pdt_enabled_help; ?></span>
						</td>
					</tr>
					<tr id="auto_return_url">
						<td class="required"><?= $entry_auto_return_url; ?></td>
						<td><input type="text" name="pp_standard_auto_return_url" size="100" value="<?= $pp_standard_auto_return_url; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_debug; ?></td>
						<td><?= $this->builder->build('select', $data_statuses, 'pp_standard_debug', $pp_standard_debug); ?></td>
					</tr>
					<tr>
						<td><?= $entry_total; ?></td>
						<td><input type="text" name="pp_standard_total" value="<?= $pp_standard_total; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_page_style; ?></td>
						<td><input type="text" name="pp_standard_page_style" value="<?= $pp_standard_page_style; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_canceled_reversal_status; ?></td>
						<? $this->builder->set_config('order_status_id', 'name'); ?>
						<td><?= $this->builder->build('select', $data_order_statuses, 'pp_standard_canceled_reversal_status_id', $pp_standard_canceled_reversal_status_id); ?></td>
					</tr>
					<tr>
						<td><?= $entry_completed_status; ?></td>
						<td><?= $this->builder->build('select', $data_order_statuses, 'pp_standard_completed_status_id', $pp_standard_completed_status_id); ?></td>
					</tr>
					<tr>
						<td><?= $entry_denied_status; ?></td>
						<td><?= $this->builder->build('select', $data_order_statuses, 'pp_standard_denied_status_id', $pp_standard_denied_status_id); ?></td>
					</tr>
					<tr>
						<td><?= $entry_expired_status; ?></td>
						<td><?= $this->builder->build('select', $data_order_statuses, 'pp_standard_expired_status_id', $pp_standard_expired_status_id); ?></td>
					</tr>
					<tr>
						<td><?= $entry_failed_status; ?></td>
						<td><?= $this->builder->build('select', $data_order_statuses, 'pp_standard_failed_status_id', $pp_standard_failed_status_id); ?></td>
					</tr>
					<tr>
						<td><?= $entry_pending_status; ?></td>
						<td><?= $this->builder->build('select', $data_order_statuses, 'pp_standard_pending_status_id', $pp_standard_pending_status_id); ?></td>
					</tr>
					<tr>
						<td><?= $entry_processed_status; ?></td>
						<td><?= $this->builder->build('select', $data_order_statuses, 'pp_standard_processed_status_id', $pp_standard_processed_status_id); ?></td>
					</tr>
					<tr>
						<td><?= $entry_refunded_status; ?></td>
						<td><?= $this->builder->build('select', $data_order_statuses, 'pp_standard_refunded_status_id', $pp_standard_refunded_status_id); ?></td>
					</tr>
					<tr>
						<td><?= $entry_reversed_status; ?></td>
						<td><?= $this->builder->build('select', $data_order_statuses, 'pp_standard_reversed_status_id', $pp_standard_reversed_status_id); ?></td>
					</tr>
					<tr>
						<td><?= $entry_voided_status; ?></td>
						<td><?= $this->builder->build('select', $data_order_statuses, 'pp_standard_voided_status_id', $pp_standard_voided_status_id); ?></td>
					</tr>
					<tr>
						<td><?= $entry_geo_zone; ?></td>
						<? $this->builder->set_config('geo_zone_id', 'name'); ?>
						<td><?= $this->builder->build('select', $data_geo_zones, 'pp_standard_geo_zone_id', $pp_standard_geo_zone_id); ?></td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><?= $this->builder->build('select', $data_statuses, 'pp_standard_status', $pp_standard_status); ?></td>
					</tr>
					<tr>
						<td><?= $entry_sort_order; ?></td>
						<td><input type="text" name="pp_standard_sort_order" value="<?= $pp_standard_sort_order; ?>" size="1" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">//<!--
$('[name=pp_standard_pdt_enabled]').change(function(){
	if ($(this).val() === '1') {
		$('#auto_return_url').show();
	} else {
		$('#auto_return_url').hide();
	}
}).change();
//--></script>
<?= $this->builder->js('errors',$errors); ?>

<?= $footer; ?> 