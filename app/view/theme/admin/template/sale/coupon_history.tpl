<table class="list">
	<thead>
		<tr>
			<td class="right"><b><?= _l("Order ID"); ?></b></td>
			<td class="left"><b><?= _l("Customer"); ?></b></td>
			<td class="right"><b><?= _l("Amount"); ?></b></td>
			<td class="left"><b><?= _l("Date Added"); ?></b></td>
		</tr>
	</thead>
	<tbody>
		<? if ($histories) { ?>
			<? foreach ($histories as $history) { ?>
				<tr>
					<td class="right"><?= $history['order_id']; ?></td>
					<td class="left"><?= $history['customer']; ?></td>
					<td class="right"><?= $history['amount']; ?></td>
					<td class="left"><?= $history['date_added']; ?></td>
				</tr>
			<? } ?>
		<? } else { ?>
			<tr>
				<td class="center" colspan="4"><?= _l("There are no results to display."); ?></td>
			</tr>
		<? } ?>
	</tbody>
</table>
<div class="pagination"><?= $pagination; ?></div>
