<table class="form">
	<tr>
		<td><?= _l("Block Status"); ?></td>
		<td><?= build('select', array(
	'name'   => "status",
	'data'   => $data_statuses,
	'select' => $status
)); ?></td>
	</tr>
</table>
