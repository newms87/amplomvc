<?= call('common/header'); ?>
<div class="section">
	<?= breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/report.png'); ?>" alt=""/> <?= _l("Products Purchased Report"); ?></h1>
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
						<td class="left"><?= _l("Product Name"); ?></td>
						<td class="left"><?= _l("Model"); ?></td>
						<td class="right"><?= _l("Quantity"); ?></td>
						<td class="right"><?= _l("Total"); ?></td>
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
							<td class="center" colspan="4"><?= _l("No results!"); ?></td>
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
			url = "<?= URL_SITE . "admin/index.php?route=report/product_purchased"; ?>";

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
<?= call('common/footer'); ?>
