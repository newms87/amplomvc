<?= _call('common/header'); ?>
<div class="section">
	<?= _breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/report.png'); ?>" alt=""/> <?= _l("Customer Reward Points Report"); ?></h1>
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
						<td class="right"><?= _l("Reward Points"); ?></td>
						<td class="right"><?= _l("No. Orders"); ?></td>
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
								<td class="right"><?= $customer['points']; ?></td>
								<td class="right"><?= $customer['orders']; ?></td>
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
			url = "<?= URL_SITE . "admin/index.php?route=report/customer_reward"; ?>";

			var filter_date_start = $('input[name=\'filter_date_start\']').attr('value');

			if (filter_date_start) {
				url += '&filter_date_start=" + encodeURIComponent(filter_date_start);
			}

			var filter_date_end = $("input[name=\'filter_date_end\']').attr('value');

			if (filter_date_end) {
				url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
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
<?= _call('common/footer'); ?>
