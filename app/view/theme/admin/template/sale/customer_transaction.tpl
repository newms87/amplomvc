<? if ($error_warning) { ?>
	<div class="message warning"><?= $error_warning; ?></div>
<? } ?>
<? if ($success) { ?>
	<div class="message success"><?= $success; ?></div>
<? } ?>
<table class="list">
	<thead>
		<tr>
			<td class="left"><?= _l("Date Added"); ?></td>
			<td class="left"><?= _l("Description"); ?></td>
			<td class="right"><?= _l("Amount"); ?></td>
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
				<td class="right"><b><?= _l("Balance"); ?></b></td>
				<td class="right"><?= $balance; ?></td>
			</tr>
		<? } else { ?>
			<tr>
				<td class="center" colspan="3"><?= _l("There are no results to display."); ?></td>
			</tr>
		<? } ?>
	</tbody>
</table>
<div class="pagination"><?= $pagination; ?></div>
