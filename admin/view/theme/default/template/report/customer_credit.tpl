<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'report.png'; ?>" alt=""/> <?= $head_title; ?></h1>
			</div>
			<div class="section">
				<table class="list">
					<thead>
					<tr>
						<td class="left"><?= $column_customer; ?></td>
						<td class="left"><?= $column_email; ?></td>
						<td class="left"><?= $column_customer_group; ?></td>
						<td class="left"><?= $column_status; ?></td>
						<td class="right"><?= $column_total; ?></td>
						<td class="right"><?= $column_action; ?></td>
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
							<td class="center" colspan="6"><?= $text_no_results; ?></td>
						</tr>
					<? } ?>
					</tbody>
				</table>
				<div class="pagination"><?= $pagination; ?></div>
			</div>
		</div>
	</div>
<?= $footer; ?>