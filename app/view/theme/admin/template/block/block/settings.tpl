<table class="form">
	<tr>
		<td>{{Block Status}}</td>
		<td><?= build(array(
				'type'   => 'select',
				'name'   => "status",
				'data'   => $data_statuses,
				'select' => $status
			)); ?></td>
	</tr>
</table>
