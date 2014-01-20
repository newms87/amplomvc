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
			<td class="right"><?= $column_points; ?></td>
		</tr>
	</thead>
	<tbody>
		<? if ($rewards) { ?>
			<? foreach ($rewards as $reward) { ?>
				<tr>
					<td class="left"><?= $reward['date_added']; ?></td>
					<td class="left"><?= $reward['description']; ?></td>
					<td class="right"><?= $reward['points']; ?></td>
				</tr>
			<? } ?>
			<tr>
				<td></td>
				<td class="right"><b><?= $text_balance; ?></b></td>
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
