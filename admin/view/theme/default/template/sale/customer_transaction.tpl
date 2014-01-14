<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
<? } ?>
<? if ($success) { ?>
	<div class="message_box success"><?= $success; ?></div>
<? } ?>
<table class="list">
	<thead>
		<tr>
			<td class="left"><?= $column_date_added; ?></td>
			<td class="left"><?= $column_description; ?></td>
			<td class="right"><?= $column_amount; ?></td>
		</tr>
	</thead>
	<tbody>
		<? if ($transactions) { ?>
			<? foreach ($transactions as $transaction) { ?>
				<tr>
					<td class="left"><?= $transaction['date_added']; ?></td>
					<td class="left"><?= $transaction['description']; ?></td>
					<td class="right"><?= $transaction['amount']; ?></td>
				</tr>
			<? } ?>
			<tr>
				<td>&nbsp;</td>
				<td class="right"><b><?= $text_balance; ?></b></td>
				<td class="right"><?= $balance; ?></td>
			</tr>
		<? } else { ?>
			<tr>
				<td class="center" colspan="3"><?= $text_no_results; ?></td>
			</tr>
		<? } ?>
	</tbody>
</table>
<div class="pagination"><?= $pagination; ?></div>
