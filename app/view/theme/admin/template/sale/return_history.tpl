<? if ($error_warning) { ?>
	<div class="message warning"><?= $error_warning; ?></div>
<? } ?>
<? if ($success) { ?>
	<div class="message success"><?= $success; ?></div>
<? } ?>
<table class="list">
	<thead>
		<tr>
			<td class="left"><b><?= _l("Date Added"); ?></b></td>
			<td class="left"><b><?= _l("Comment"); ?></b></td>
			<td class="left"><b><?= _l("Status"); ?></b></td>
			<td class="left"><b><?= _l("Notify"); ?></b></td>
		</tr>
	</thead>
	<tbody>
		<? if ($histories) { ?>
			<? foreach ($histories as $history) { ?>
				<tr>
					<td class="left"><?= $history['date_added']; ?></td>
					<td class="left"><?= $history['comment']; ?></td>
					<td class="left"><?= $history['status']; ?></td>
					<td class="left"><?= $history['notify']; ?></td>
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
