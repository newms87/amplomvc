<table class="form">
	<tr>
		<td><?= _l("Instance Name"); ?></td>
		<td>
			<? //NOTE: .instance_name class is used to link the tab name to this instance's settings. ?>
			<input type="text" class="tab_name instance_name" name="instances[<?= $row; ?>][name]" value="<?= $instance['name']; ?>"/>
		</td>
	</tr>
	<tr>
		<td><?= _l("Show Title?"); ?></td>
		<td><?= $this->builder->build('radio', $data_yes_no, "instances[$row][show_title]", $instance['show_title']); ?></td>
	</tr>
</table>
