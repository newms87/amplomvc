<div id="profile_tab_list" class="vtabs">
	<div id="add_profile" class="add-vtab"><?= _l("New Profile"); ?></div>

	<? foreach ($profiles as $row => $profile) { ?>
		<a href="#tab-profile-<?= $row; ?>" data-row="<?= $row; ?>">
			<span class="tab_name"><?= $profile['name']; ?></span>
			<img src="<?= URL_THEME_IMAGE . 'delete.png'; ?>" class="delete_tab"/>
		</a>
	<? } ?>
</div>

<div id="profile_list">
	<? foreach ($profiles as $row => $profile) { ?>
		<div id="tab-profile-<?= $row; ?>" data-row="<?= $row; ?>" class="vtabs-content profile">
			<table class="form">
				<tr>
					<td><?= _l("Profile Name"); ?></td>
					<td>
						<input type="text" class="tab_name" name="profiles[<?= $row; ?>][name]" value="<?= $profile['name']; ?>"/>
					</td>
				</tr>
				<tr>
					<td><?= _l("Instance"); ?></td>
					<td>
						<? $this->builder->setConfig(false, "name"); ?>
						<?= $this->builder->build('select', $data_instances, "profiles[$row][block_instance_id]", $profile['block_instance_id']); ?>
					</td>
				</tr>
				<tr>
					<td><?= _l("Stores"); ?></td>
					<td>
						<? $this->builder->setConfig("store_id", "name"); ?>
						<?= $this->builder->build('multiselect', $data_stores, "profiles[$row][store_ids]", !empty($profile['store_ids']) ? $profile['store_ids'] : null); ?>
					</td>
				</tr>
				<tr>
					<td><?= _l("Layouts"); ?></td>
					<td>
						<? $this->builder->setConfig("layout_id", "name"); ?>
						<?= $this->builder->build('multiselect', $data_layouts, "profiles[$row][layout_ids]", !empty($profile['layout_ids']) ? $profile['layout_ids'] : null); ?>
					</td>
				</tr>
				<tr>
					<td><?= _l("Positions"); ?></td>
					<td>
						<?= $this->builder->build('select', $data_positions, "profiles[$row][position]", $profile['position']); ?>
					</td>
				</tr>
				<tr>
					<td><?= _l("Profile Status"); ?></td>
					<td>
						<?= $this->builder->build('select', $data_statuses, "profiles[$row][status]", $profile['status']); ?>
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
