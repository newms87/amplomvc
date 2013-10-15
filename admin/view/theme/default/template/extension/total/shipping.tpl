<table class="form">
	<tr>
		<td><?= $entry_estimator; ?></td>
		<td><?= $this->builder->build('select', $data_statuses, "settings[estimator]", $settings['estimator']); ?></td>
	</tr>
</table>
