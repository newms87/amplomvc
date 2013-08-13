<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'report.png'; ?>" alt="" /> <?= $head_title; ?></h1>
		</div>
		<div class="content">
			<table class="form">
				<tr>
					<td><?= $entry_date_start; ?>
						<input type="text" name="filter_date_start" value="<?= $filter_date_start; ?>" id="date-start" size="12" /></td>
					<td><?= $entry_date_end; ?>
						<input type="text" name="filter_date_end" value="<?= $filter_date_end; ?>" id="date-end" size="12" /></td>
					<td><?= $entry_status; ?>
						<select name="filter_order_status_id">
							<option value="0"><?= $text_all_status; ?></option>
							<? foreach ($order_statuses as $order_status) { ?>
							<? if ($order_status['order_status_id'] == $filter_order_status_id) { ?>
							<option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
							<? } else { ?>
							<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
							<? } ?>
							<? } ?>
						</select></td>
					<td style="text-align: right;"><a onclick="filter();" class="button"><?= $button_filter; ?></a></td>
				</tr>
			</table>
			<table class="list">
				<thead>
					<tr>
						<td class="left"><?= $column_name; ?></td>
						<td class="left"><?= $column_model; ?></td>
						<td class="right"><?= $column_quantity; ?></td>
						<td class="right"><?= $column_total; ?></td>
					</tr>
				</thead>
				<tbody>
					<? if ($products) { ?>
					<? foreach ($products as $product) { ?>
					<tr>
						<td class="left"><?= $product['name']; ?></td>
						<td class="left"><?= $product['model']; ?></td>
						<td class="right"><?= $product['quantity']; ?></td>
						<td class="right"><?= $product['total']; ?></td>
					</tr>
					<? } ?>
					<? } else { ?>
					<tr>
						<td class="center" colspan="4"><?= $text_no_results; ?></td>
					</tr>
					<? } ?>
				</tbody>
			</table>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<script type="text/javascript"><!--
function filter() {
	url = "<?= HTTP_ADMIN . "index.php?route=report/product_purchased"; ?>";
	
	var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');
	
	if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}

	var filter_date_end = $('input[name=\'filter_date_end\']').attr('value');
	
	if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}
	
	var filter_order_status_id = $('select[name=\'filter_order_status_id\']').attr('value');
	
	if (filter_order_status_id) {
		url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
	}

	location = url;
}
//--></script>
<script type="text/javascript"><!--
$(document).ready(function() {
	$('#date-start').datepicker({dateFormat: 'yy-mm-dd'});
	
	$('#date-end').datepicker({dateFormat: 'yy-mm-dd'});
});
//--></script>
<?= $footer; ?>