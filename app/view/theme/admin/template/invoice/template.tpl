<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="invoice-page row">
	<div class="invoice-actions col xs-12">
		<a href="<?= site_url('admin/invoice/form', 'customer_id=' . $customer['customer_id']); ?>" class="button back">Back</a>
		<button id="download">Download</button>
	</div>

	<div class="invoice-preview col xs-12">
		<div id="client-invoice" class="client-invoice-template col xs-12">
			<div class="header row">
				<div class="col xs-12 logo">
					<img <?= img(theme_image('logo.png', 250)); ?> />
				</div>
			</div>

			<div class="row scopetech-details">
				<div class="col xs-6 left address">
					<p contenteditable="">
						Scope Technologies, Inc.<br/>
						1630 Stout Street<br/>
						Denver, CO 80202
					</p>
					<br/>

					<p contenteditable="">
						877 697 2673<br/>
						invoice@myscopetech.com<br/>
						www.roofscope.com
					</p>
				</div>

				<div class="col xs-6 right invoice-info">
					<h2>Invoice</h2>

					<div class="invoice-tables">
						<table class="invoice-date">
							<thead>
							<tr>
								<td>Date</td>
								<td>Invoice No.</td>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td contenteditable><?= date('m/d/Y'); ?></td>
								<td contenteditable><?= $number; ?></td>
							</tr>
							</tbody>
						</table>

						<table class="invoice-terms">
							<thead>
							<tr>
								<td>Terms</td>
								<td>Due Date</td>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td contenteditable>Due on receipt</td>
								<td contenteditable><?= $date_due !== DATETIME_ZERO ? format('date', $date_due, 'm/d/Y') : 'N/A'; ?></td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="row billing-info">
				<div class="bill-to col xs-6 left bottom">
					<table class="invoice-terms">
						<thead>
						<tr>
							<td>Bill To</td>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td contenteditable><?= format('address', $customer['address']); ?></td>
						</tr>
						</tbody>
					</table>
				</div>

				<div class="bill-amount col xs-6 right bottom">
					<table class="invoice-amount">
						<thead>
						<tr>
							<td>Amount Due</td>
							<td>Enclosed</td>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td contenteditable><?= format('currency', $total); ?></td>
							<td contenteditable></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>

			<div class="row cut-separator">
				<div class="text">{{Please detach top portion and return with your payment}}</div>
				<img <?= img(theme_image('scissors.png')); ?> />
			</div>

			<div class="row invoice-list">
				<table class="line-items">
					<thead>
					<tr>
						<td class="date">{{Date}}</td>
						<td class="user-id">{{User}}</td>
						<td class="item-label">{{Project}}</td>
						<td class="type">{{Type}}</td>
						<td class="amount">{{Amount}}</td>
					</tr>
					</thead>

					<tbody>
					<? foreach ($orders as $order) { ?>
						<tr>
							<td class="date"><?= format('date', $order['date_created'], 'm/d/Y'); ?></td>
							<td class="user-id"><?= $this->Model_Client->getField($order['client_id'], 'username'); ?></td>
							<td class="item-label"><?= $order['name']; ?></td>
							<td class="type"><?= Tracescope::getScopeType($order['scope_type_id']); ?></td>
							<td class="amount"><?= format('currency', $order['price']); ?></td>
						</tr>
					<? } ?>
					</tbody>
				</table>

			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#download').click(function () {
		window.print();
	});
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
