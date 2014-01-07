<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'report.png'; ?>" alt=""/> <?= _l("Shipping Report"); ?></h1>
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
						<td><?= _l("Group By:"); ?>
							<select name="filter_group">
								<? foreach ($groups as $groups) { ?>
									<? if ($groups['value'] == $filter_group) { ?>
										<option value="<?= $groups['value']; ?>"
										        selected="selected"><?= $groups['text']; ?></option>
									<? } else { ?>
										<option value="<?= $groups['value']; ?>"><?= $groups['text']; ?></option>
									<? } ?>
								<? } ?>
							</select></td>
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
						<td class="left"><?= _l("Date Start"); ?></td>
						<td class="left"><?= _l("Date End"); ?></td>
						<td class="left"><?= _l("Shipping Title"); ?></td>
						<td class="right"><?= _l("No. Orders"); ?></td>
						<td class="right"><?= _l("Total"); ?></td>
					</tr>
					</thead>
					<tbody>
					<? if ($orders) { ?>
						<? foreach ($orders as $order) { ?>
							<tr>
								<td class="left"><?= $order['date_start']; ?></td>
								<td class="left"><?= $order['date_end']; ?></td>
								<td class="left"><?= $order['title']; ?></td>
								<td class="right"><?= $order['orders']; ?></td>
								<td class="right"><?= $order['total']; ?></td>
							</tr>
						<? } ?>
					<? } else { ?>
						<tr>
							<td class="center" colspan="5"><?= _l("No results!"); ?></td>
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
			url = "<?= HTTP_ADMIN . "index.php?route=report/sale_shipping"; ?>";

			var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');

			if (filter_date_start) {
				url += '&filter_date_start=" + encodeURIComponent(filter_date_start);
			}

			var filter_date_end = $("input[name=\'filter_date_end\']').attr('value');

			if (filter_date_end) {
				url += '&filter_date_end=" + encodeURIComponent(filter_date_end);
			}

			var filter_group = $("select[name=\'filter_group\']').attr('value');

			if (filter_group) {
				url += '&filter_group=" + encodeURIComponent(filter_group);
			}

			var filter_order_status_id = $("select[name=\'filter_order_status_id\']').attr('value');

			if (filter_order_status_id) {
				url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
			}

			location = url;
		}
</script>
	<script type="text/javascript"><!--
		$(document).ready(function () {
			$('#date-start').datepicker({dateFormat: 'yy-mm-dd'});

			$('#date-end').datepicker({dateFormat: 'yy-mm-dd'});
		});
</script>
<?= $footer; ?>
