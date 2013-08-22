<table class="list">
	<thead>
	<tr>
		<td class="right"><b><?= $column_order_id; ?></b></td>
		<td class="left"><b><?= $column_customer; ?></b></td>
		<td class="right"><b><?= $column_amount; ?></b></td>
		<td class="left"><b><?= $column_date_added; ?></b></td>
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
			<td class="center" colspan="4"><?= $text_no_results; ?></td>
		</tr>
	<? } ?>
	</tbody>
</table>
<div class="pagination"><?= $pagination; ?></div>
