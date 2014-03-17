<?= $common_header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'home.png'; ?>" alt=""/> <?= _l("Dashboard"); ?></h1>
		</div>
		<div class="section">
			<div class="overview">
				<div class="dashboard-heading"><?= _l("Overview"); ?></div>
				<div class="dashboard-content">
					<table>
						<tr>
							<td><?= _l("Total Sales:"); ?></td>
							<td><?= $total_sale; ?></td>
						</tr>
						<tr>
							<td><?= _l("Total Sales This Year:"); ?></td>
							<td><?= $total_sale_year; ?></td>
						</tr>
						<tr>
							<td><?= _l("Total Orders:"); ?></td>
							<td><?= $total_order; ?></td>
						</tr>
						<tr>
							<td><?= _l("No. of Customers:"); ?></td>
							<td><?= $total_customer; ?></td>
						</tr>
						<tr>
							<td><?= _l("Customers Awaiting Approval:"); ?></td>
							<td><?= $total_customer_approval; ?></td>
						</tr>
						<tr>
							<td><?= _l("Reviews Awaiting Approval:"); ?></td>
							<td><?= $total_review_approval; ?></td>
						</tr>
					</table>
				</div>
			</div>
			<div class="statistic">
				<div class="range"><?= _l("Select Range:"); ?>
					<select id="range" onchange="getSalesChart(this.value)">
						<option value="day"><?= _l("Today"); ?></option>
						<option value="week"><?= _l("This Week"); ?></option>
						<option value="month"><?= _l("This Month"); ?></option>
						<option value="year"><?= _l("This Year"); ?></option>
					</select>
				</div>
				<div class="dashboard-heading"><?= _l("Statistics"); ?></div>
				<div class="dashboard-content">
					<div id="report" style="width: 390px; height: 170px; margin: auto;"></div>
				</div>
			</div>
			<div class="latest">
				<div class="dashboard-heading"><?= _l("Latest 10 Orders"); ?></div>
				<div class="dashboard-content">
					<table class="list">
						<thead>
							<tr>
								<td class="right"><?= _l("Order ID"); ?></td>
								<td class="left"><?= _l("Customer"); ?></td>
								<td class="left"><?= _l("Status"); ?></td>
								<td class="left"><?= _l("Date Added"); ?></td>
								<td class="right"><?= _l("Total"); ?></td>
								<td class="right"><?= _l("Action"); ?></td>
							</tr>
						</thead>
						<tbody>
							<? if ($orders) { ?>
								<? foreach ($orders as $order) { ?>
									<tr>
										<td class="right"><?= $order['order_id']; ?></td>
										<td
											class="left"><?= $order['customer']['firstname'] . ' ' . $order['customer']['lastname']; ?></td>
										<td class="left"><?= $order['order_status']['title']; ?></td>
										<td class="left"><?= $order['date_added']; ?></td>
										<td class="right"><?= $order['total']; ?></td>
										<td class="right">
											<? foreach ($order['action'] as $action) { ?>
												[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
											<? } ?>
										</td>
									</tr>
								<? } ?>
							<? } else { ?>
								<tr>
									<td class="center" colspan="6"><?= _l("No results!"); ?></td>
								</tr>
							<? } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!--[if IE]>
<script type="text/javascript" src="<?= URL_RESOURCES . 'js/jquery/flot/excanvas.js'; ?>"></script>
<![endif]-->
<script type="text/javascript" src="<?= URL_RESOURCES . 'js/jquery/flot/jquery.flot.js'; ?>"></script>
<script type="text/javascript">
	function getSalesChart(range) {
		$.ajax({
			type: 'GET',
			url: "<?= $url_chart; ?>" + '&range=" + range,
			dataType: "json',
			async: false,
			success: function (json) {
				var option = {
					shadowSize: 0,
					lines: {
						show: true,
						fill: true,
						lineWidth: 1
					},
					grid: {
						backgroundColor: '#FFFFFF'
					},
					xaxis: {
						ticks: json.xaxis
					}
				}

				$.plot($('#report'), [json.order, json.customer], option);
			}
		});
	}

	getSalesChart($('#range').val());
</script>
<?= $common_footer; ?>
