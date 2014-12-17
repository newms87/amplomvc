<div id="profile_tab_list" class="vtabs">
	<div id="add_profile" class="add-vtab">{{New Profile}}</div>

	<? foreach ($profiles as $row => $profile) { ?>
		<a href="#tab-profile-<?= $row; ?>" data-row="<?= $row; ?>">
			<span class="tab_name"><?= $profile['name']; ?></span>
			<img src="<?= theme_url('image/delete.png'); ?>" class="delete_tab"/>
		</a>
	<? } ?>
</div>

<div id="profile_list">
	<? foreach ($profiles as $row => $profile) { ?>
		<div id="tab-profile-<?= $row; ?>" data-row="<?= $row; ?>" class="vtabs-content profile">
			<table class="form">
				<tr>
					<td>{{Profile Name}}</td>
					<td>
						<input type="text" class="tab_name" name="profiles[<?= $row; ?>][name]" value="<?= $profile['name']; ?>"/>
					</td>
				</tr>
				<tr>
					<td>{{Instance}}</td>
					<td>
						<?=
						build(array(
							'type' => 'select',
							'name'  => "profiles[$row][block_instance_id]",
							'data'   => $data_instances,
							'select' => $profile['block_instance_id'],
							'key'    => false,
							'value'  => "name",
						)); ?>
					</td>
				</tr>
				<tr>
					<td>{{Layouts}}</td>
					<td>
						<?=
						build(array(
							'type' => 'multiselect',
							'name'  => "profiles[$row][layout_ids]",
							'data'   => $data_layouts,
							'select' => !empty($profile['layout_ids']) ? $profile['layout_ids'] : null,
							'key'    => "layout_id",
							'value'  => "name",
						)); ?>
					</td>
				</tr>
				<tr>
					<td>{{Positions}}</td>
					<td><?=
						build(array(
							'type' => 'select',
							'name'  => "profiles[$row][position]",
							'data'   => $data_positions,
							'select' => $profile['position']
						)); ?>
					</td>
				</tr>
				<tr>
					<td>{{Profile Status}}</td>
					<td><?=
						build(array(
							'type' => 'select',
							'name'  => "profiles[$row][status]",
							'data'   => $data_statuses,
							'select' => $profile['status']
						)); ?>
					</td>
				</tr>
			</table>
		</div>
	<? } ?>
</div>

<script type="text/javascript">
	//Delete Tab
	$('#profile_tab_list .delete_tab').click(function () {
		var $tab = $(this).closest('a');

		if ($tab.hasClass('selected')) {
			$tab.siblings('a:first').click();
		}

		$($tab.attr('href')).remove();
		$tab.remove();

		return false;
	});

	//Profile
	$('#profile_tab_list').ac_template('profile_tab_list');
	$('#profile_list').ac_template('profile_list', {defaults: <?= json_encode($profiles['__ac_template__']); ?>});

	$('#add_profile').click(function () {
		var ptab = $.ac_template('profile_tab_list', 'add');
		$.ac_template('profile_list', 'add');

		$('#profile_tab_list a').tabs();

		ptab.click();
	});

	//Tabs
	$('#profile_tab_list a').tabs();
</script>
