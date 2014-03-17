<?= $common_header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'report.png'; ?>" alt=""/> <?= _l("Customer Orders Report"); ?></h1>
		</div>
		<div class="section">
			<table class="form">
				<tr>
					<td><?= _l("Date Start:"); ?>
						<input type="text" name="filter_date_start" value="<?= $filter_date_start; ?>" id="date-start"
							size="12"/></td>
					<td><?= _l("Date End:"); ?>
						<input type="text" name="filter_date_end" value="<?= $filter_date_end; ?>" id="date-end"
							size="12"/></td>
					<td><?= _l("Order Status:"); ?>
						<select name="filter_order_status_id">
							<option value="0"><?= _l("All Statuses"); ?></option>
							<? foreach ($order_statuses as $order_status) { ?>
								<? if ($order_status['order_status_id'] == $filter_order_status_id) { ?>
									<option value="<?= $order_status['order_status_id']; ?>"
										selected="selected"><?= $order_status['name']; ?></option>
								<? } else { ?>
									<option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
								<? } ?>
							<? } ?>
						</select></td>
					<td style="text-align: right;"><a onclick="filter();" class="button"><?= _l("Filter"); ?></a></td>
				</tr>
			</table>
			<table class="list">
				<thead>
					<tr>
						<td class="left"><?= _l("Customer Name"); ?></td>
						<td class="left"><?= _l("E-Mail"); ?></td>
						<td class="left"><?= _l("Customer Group"); ?></td>
						<td class="left"><?= _l("Status"); ?></td>
						<td class="right"><?= _l("No. Orders"); ?></td>
						<td class="right"><?= _l("No. Products"); ?></td>
						<td class="right"><?= _l("Total"); ?></td>
						<td class="right"><?= _l("Action"); ?></td>
					</tr>
				</thead>
				<tbody>
					<? if ($customers) { ?>
						<? foreach ($customers as $customer) { ?>
							<tr>
								<td class="left"><?= $customer['customer']; ?></td>
								<td class="left"><?= $customer['email']; ?></td>
								<td class="left"><?= $customer['customer_group']; ?></td>
								<td class="left"><?= $customer['status']; ?></td>
								<td class="right"><?= $customer['orders']; ?></td>
								<td class="right"><?= $customer['products']; ?></td>
								<td class="right"><?= $customer['total']; ?></td>
								<td class="right"><? foreach ($customer['action'] as $action) { ?>
										[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
									<? } ?></td>
							</tr>
						<? } ?>
					<? } else { ?>
						<tr>
							<td class="center" colspan="8"><?= _l("No results!"); ?></td>
						</tr>
					<? } ?>
				</tbody>
			</table>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<script type="text/javascript"><
	!--
		function filter() {
			url = "<?= URL_SITE . "admin/index.php?route=report/customer_order"; ?>";

			var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');

			if (filter_date_start) {
				url += '&filter_date_start=" + encodeURIComponent(filter_date_start);
			}

			var filter_date_end = $("input[name=\'filter_date_end\']').attr('value');

			if (filter_date_end) {
				url += '&filter_date_end=" + encodeURIComponent(filter_date_end);
			}

			var filter_order_status_id = $("select[name=\'filter_order_status_id\']').attr('value');

			if (filter_order_status_id) {
				url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
			}

			location = url;
		}
</script>
<script type="text/javascript"><
	!--
		$(document).ready(function () {
			$('#date-start').datepicker({dateFormat: 'yy-mm-dd'});

			$('#date-end').datepicker({dateFormat: 'yy-mm-dd'});
		});
</script>
<?= $common_footer; ?>
