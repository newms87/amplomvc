<table class="form">
	<tr>
		<td>{{Block Status}}</td>
		<td><?= build('select', array(
	'name'   => "status",
	'data'   => $data_statuses,
	'select' => $status
)); ?></td>
	</tr>
</table>
