<table class="form">
	<tr>
		<td><?= _l("Shipping Estimator:"); ?></td>
		<td><?= $this->builder->build('select', $data_statuses, "settings[estimator]", $settings['estimator']); ?></td>
	</tr>
</table>
