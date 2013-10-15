<?= $header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt=""/> <?= $head_title; ?></h1>

			<div class="buttons">
				<a onclick="$('#form').trigger('saving').submit();" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="section">
			<div id="tabs" class="htabs">
				<a href="#tab-settings"><?= $tab_settings; ?></a>
				<a href="#tab-profile-settings"><?= $tab_profile_settings; ?></a>
				<a href="#tab-profile"><?= $tab_profile; ?></a>
			</div>
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">

				<div id='tab-settings'>
					<table class="form">
						<? if (!empty($extend_settings)) { ?>
							<tr>
								<td colspan="2"><?= $extend_settings; ?></td>
							</tr>
						<? } ?>
						<tr>
							<td><?= $entry_block_status; ?></td>
							<td><?= $this->builder->build('select', $data_statuses, "status", $status); ?></td>
						</tr>
					</table>
				</div>

				<div id='tab-profile-settings'>
					<div id="profile_settings_tab_list" class="vtabs">
						<span id="add_profile_setting">
							<span><?= $button_add_profile_setting; ?></span>
							<img src="<?= HTTP_THEME_IMAGE . 'add.png'; ?>" alt="" />
						</span>

						<? foreach ($profile_settings as $row => $profile_setting) { ?>
							<a href="#tab-profile-setting-<?= $row; ?>" data-row="<?= $row; ?>">
								<span class="tab_name"><?= $profile_setting['name']; ?></span>
								<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" onclick="return false" class="delete_tab" />
							</a>
						<? } ?>
					</div>

					<div id="profile_settings_list">
						<? foreach ($profile_settings as $row => $profile_setting) { ?>
							<div id="tab-profile-setting-<?= $row; ?>" data-row="<?= $row; ?>" class="vtabs-content profile_setting">
								<table class="form">
									<tr>
										<td><?= $entry_profile_setting_name; ?></td>
										<td><input type="text" class="tab_name profile_setting_name" name="profile_settings[<?= $row; ?>][name]" value="<?= $profile_setting['name']; ?>" /></td>
									</tr>
									<tr>
										<td><?= $entry_show_block_title; ?></td>
										<td><?= $this->builder->build('radio', $data_yes_no, "profile_settings[$row][show_block_title]", $profile_setting['show_block_title']); ?></td>
									</tr>
								</table>
							</div>
						<? } ?>
					</div>

					<? if (!empty($extend_profile_settings)) { ?>
						<div id="extend_profile_settings">
							<?= $extend_profile_settings; ?>
						</div>
					<? } ?>
				</div>

				<div id='tab-profile'>
					<div id="profile_tab_list" class="vtabs">
						<span id="add_profile">
							<span><?= $button_add_profile; ?></span>
							<img src="<?= HTTP_THEME_IMAGE . 'add.png'; ?>" />
						</span>

						<? foreach ($profiles as $row => $profile) { ?>
							<a href="#tab-profile-<?= $row; ?>" data-row="<?= $row; ?>">
								<span class="tab_name"><?= $profile['name']; ?></span>
								<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" class="delete_tab" />
							</a>
						<? } ?>
					</div>

					<div id="profile_list">
						<? foreach ($profiles as $row => $profile) { ?>
							<div id="tab-profile-<?= $row; ?>" data-row="<?= $row; ?>" class="vtabs-content profile">
								<table class="form">
									<tr>
										<td><?= $entry_profile_name; ?></td>
										<td><input type="text" class="tab_name" name="profiles[<?= $row; ?>][name]" value="<?= $profile['name']; ?>" /></td>
									</tr>
									<tr>
										<td><?= $entry_profile_setting_id; ?></td>
										<td>
											<? $this->builder->setConfig(false, "name"); ?>
											<?= $this->builder->build('select', $data_profile_settings, "profiles[$row][profile_setting_id]", $profile['profile_setting_id']); ?>
										</td>
									</tr>
									<tr>
										<td><?= $entry_store; ?></td>
										<td>
											<? $this->builder->setConfig("store_id", "name"); ?>
											<?= $this->builder->build('multiselect', $data_stores, "profiles[$row][store_ids]", !empty($profile['store_ids']) ? $profile['store_ids'] : null); ?>
										</td>
									</tr>
									<tr>
										<td><?= $entry_layout; ?></td>
										<td>
											<? $this->builder->setConfig("layout_id", "name"); ?>
											<?= $this->builder->build('multiselect', $data_layouts, "profiles[$row][layout_ids]", !empty($profile['layout_ids']) ? $profile['layout_ids'] : null); ?>
										</td>
									</tr>
									<tr>
										<td><?= $entry_position; ?></td>
										<td>
											<?= $this->builder->build('select', $data_positions, "profiles[$row][position]", $profile['position']); ?>
										</td>
									</tr>
									<tr>
										<td><?= $entry_profile_status; ?></td>
										<td>
											<?= $this->builder->build('select', $data_statuses, "profiles[$row][status]", $profile['status']); ?>
										</td>
									</tr>
								</table>
							</div>
						<? } ?>
					</div>
				</div>

			</form>
		</div>
	</div>
</div>

<script type="text/javascript">//<!--
$('[data-extend]').each(function(i,e){
	$(e).appendTo('#'+$(e).attr('data-extend'));
});

//Update Tab Name
$('.profile_setting_name').keyup(function(){
	update_profile_setting_select();
});

//Delete Tab
$('.delete_tab').click(function(){
	if ($(this).closest('a').hasClass('selected')) {
		$(this).closest('.vtabs').children('a:first').click();
	}

	var tab = $(this).closest('a');

	$(tab.attr('href')).remove();
	tab.remove();

	update_profile_setting_select();

	return false;
});

//Profile Settings
$('#profile_settings_tab_list').ac_template('profile_settings_tab_list');
$('#profile_settings_list').ac_template('profile_settings_list', {defaults: <?= json_encode($profile_settings['__ac_template__']); ?>});

$('#add_profile_setting').click(function(){
	var pstab = $.ac_template('profile_settings_tab_list', 'add');
	$.ac_template('profile_settings_list', 'add');

	pstab.closest('.vtabs').children('a').tabs();
	pstab.click();

	update_profile_setting_select();
});

function update_profile_setting_select(){
	profile_template = $.ac_template.templates['profile_list'].template.find('select[name*=profile_setting_id]');
	context = $('select[name*=profile_setting_id]').add(profile_template);

	var options = '';

	$('#profile_settings_tab_list a').each(function(i,e){
		options += '<option value="'+$(e).attr('data-row')+'">'+$(e).find('.tab_name').html()+'</option>';
	});

	context.html(options);
}

//Profile
$('#profile_tab_list').ac_template('profile_tab_list');
$('#profile_list').ac_template('profile_list', {defaults: <?= json_encode($profiles['__ac_template__']); ?>});

$('#add_profile').click(function(){
	var ptab = $.ac_template('profile_tab_list', 'add');
	$.ac_template('profile_list', 'add');

	ptab.closest('.vtabs').children('a').tabs();
	ptab.click();
});

//Tabs
$('#tabs a').tabs();
$('.vtabs').each(function(i,e){
	$(e).children('a').tabs();
});
//--></script>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
