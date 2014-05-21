<table class="form">
	<tr>
		<td><?= _l("Block Status"); ?></td>
		<td><?= $this->builder->build('select', $data_statuses, "status", $status); ?></td>
	</tr>
</table>
