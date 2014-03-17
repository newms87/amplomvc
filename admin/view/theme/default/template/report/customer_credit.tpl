<?= $common_header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'report.png'; ?>" alt=""/> <?= _l("Customer Credit Report"); ?></h1>
		</div>
		<div class="section">
			<table class="list">
				<thead>
					<tr>
						<td class="left"><?= _l("Customer Name"); ?></td>
						<td class="left"><?= _l("E-Mail"); ?></td>
						<td class="left"><?= _l("Customer Group"); ?></td>
						<td class="left"><?= _l("Status"); ?></td>
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
								<td class="right"><?= $customer['total']; ?></td>
								<td class="right"><? foreach ($customer['action'] as $action) { ?>
										[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
									<? } ?></td>
							</tr>
						<? } ?>
					<? } else { ?>
						<tr>
							<td class="center" colspan="6"><?= _l("No results!"); ?></td>
						</tr>
					<? } ?>
				</tbody>
			</table>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<?= $common_footer; ?>
