<table class="form">
	<tr>
		<td>{{Instance Name}}</td>
		<td>
			<? //NOTE: .instance_name class is used to link the tab name to this instance's settings. ?>
			<input type="text" class="tab_name instance_name" name="instances[<?= $row; ?>][name]" value="<?= $instance['name']; ?>"/>
		</td>
	</tr>
	<tr>
		<td>{{Show Title?}}</td>
		<td><?=
			build('radio', array(
				'name'   => "instances[$row][show_title]",
				'data'   => $data_yes_no,
				'select' => $instance['show_title']
			)); ?></td>
	</tr>
</table>
