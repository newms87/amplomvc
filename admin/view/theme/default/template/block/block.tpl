<?= $common_header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>

	<form id="form" class="box" action="<?= $save; ?>" method="post" enctype="multipart/form-data">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'module.png'; ?>" alt=""/> <?= _l("Blocks"); ?></h1>

			<div class="buttons">
				<button class="button"><?= _l("Save"); ?></button>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>

		<div class="section">
			<div id="tabs" class="htabs">
				<a href="#tab-settings"><?= _l("Settings"); ?></a>
				<a href="#tab-instances"><?= _l("Profile Settings"); ?></a>
				<a href="#tab-profile"><?= _l("Profiles"); ?></a>
			</div>


			<div id="tab-settings">
				<table class="form">
					<? if (!empty($extend_settings)) { ?>
						<tr>
							<td colspan="2"><?= $extend_settings; ?></td>
						</tr>
					<? } ?>
					<tr>
						<td><?= _l("Block Status"); ?></td>
						<td><?= $this->builder->build('select', $data_statuses, "status", $status); ?></td>
					</tr>
				</table>
			</div>

			<div id="tab-instances">
				<div id="instances_tab_list" class="vtabs">
						<span id="add_instance">
							<span><?= _l("New Profile Setting"); ?></span>
							<img src="<?= URL_THEME_IMAGE . 'add.png'; ?>" alt=""/>
						</span>

					<? foreach ($instances as $row => $instance) { ?>
						<a href="#tab-instance-<?= $row; ?>" data-row="<?= $row; ?>">
							<span class="tab_name"><?= $instance['name']; ?></span>
							<img src="<?= URL_THEME_IMAGE . 'delete.png'; ?>" onclick="return false" class="delete_tab"/>
						</a>
					<? } ?>
				</div>

				<div id="instances_list">
					<? foreach ($instances as $row => $instance) { ?>
						<div id="tab-instance-<?= $row; ?>" data-row="<?= $row; ?>" class="vtabs-content instance">
							<table class="form">
								<tr>
									<td><?= _l("Profile Setting Name"); ?></td>
									<td>
										<input type="text" class="tab_name instance_name" name="instances[<?= $row; ?>][name]" value="<?= $instance['name']; ?>"/>
									</td>
								</tr>
								<tr>
									<td><?= _l("Show Block Title?"); ?></td>
									<td><?= $this->builder->build('radio', $data_yes_no, "instances[$row][show_title]", $instance['show_title']); ?></td>
								</tr>
							</table>
						</div>
					<? } ?>
				</div>

				<? if (!empty($extend_instances)) { ?>
					<div id="extend_instances">
						<?= $extend_instances; ?>
					</div>
				<? } ?>
			</div>

			<div id="tab-profile">
				<div id="profile_tab_list" class="vtabs">
						<span id="add_profile">
							<span><?= _l("New Profile"); ?></span>
							<img src="<?= URL_THEME_IMAGE . 'add.png'; ?>"/>
						</span>

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
									<td><?= _l("Profile Setting"); ?></td>
									<td>
										<? $this->builder->setConfig(false, "name"); ?>
										<?= $this->builder->build('select', $data_instances, "profiles[$row][instance_id]", $profile['instance_id']); ?>
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
			</div>

		</div>
	</form>
</div>

<script type="text/javascript">
	$('[data-extend]').each(function (i, e) {
		$(e).appendTo('#' + $(e).attr('data-extend'));
	});

	//Update Tab Name
	$('.instance_name').keyup(function () {
		update_instance_select();
	});

	//Delete Tab
	$('.delete_tab').click(function () {
		if ($(this).closest('a').hasClass('selected')) {
			$(this).closest('.vtabs').children('a:first').click();
		}

		var tab = $(this).closest('a');

		$(tab.attr('href')).remove();
		tab.remove();

		update_instance_select();

		return false;
	});

	//Profile Settings
	$('#instances_tab_list').ac_template('instances_tab_list');
	$('#instances_list').ac_template('instances_list', {defaults: <?= json_encode($instances['__ac_template__']); ?>});

	$('#add_instance').click(function () {
		var pstab = $.ac_template('instances_tab_list', 'add');
		$.ac_template('instances_list', 'add');

		pstab.closest('.vtabs').children('a').tabs();
		pstab.click();

		update_instance_select();
	});

	function update_instance_select() {
		profile_template = $.ac_template.templates['profile_list'].template.find('select[name*=instance_id]');
		context = $('select[name*=instance_id]').add(profile_template);

		var options = '';

		$('#instances_tab_list a').each(function (i, e) {
			options += '<option value="' + $(e).attr('data-row') + '">' + $(e).find('.tab_name').html() + '</option>';
		});

		context.html(options);
	}

	//Profile
	$('#profile_tab_list').ac_template('profile_tab_list');
	$('#profile_list').ac_template('profile_list', {defaults: <?= json_encode($profiles['__ac_template__']); ?>});

	$('#add_profile').click(function () {
		var ptab = $.ac_template('profile_tab_list', 'add');
		$.ac_template('profile_list', 'add');

		ptab.closest('.vtabs').children('a').tabs();
		ptab.click();
	});

	//Tabs
	$('#tabs a').tabs();
	$('.vtabs').each(function (i, e) {
		$(e).children('a').tabs();
	});
</script>

<?= $this->builder->js('errors', $errors); ?>

<?= $common_footer; ?>
