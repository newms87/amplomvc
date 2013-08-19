<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt=""/> <?= $head_title; ?></h1>

			<div class="buttons">
				<a onclick="$('#form').trigger('saving').submit();" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<div id="tabs" class="htabs">
				<a href="#tab-settings"><?= $tab_settings; ?></a>
				<a href="#tab-profile"><?= $tab_profile; ?></a>
			</div>
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">

				<div id='tab-settings'>
					<table class="form">
						<? if (!empty($extend_settings)) { ?>
							<tr>
								<td colspan="2" style="border:none;padding: 0;"><?= $extend_settings; ?></td>
							</tr>
						<? } ?>
						<tr>
							<td><?= $entry_block_status; ?></td>
							<td><?= $this->builder->build('select', $data_statuses, "status", $status); ?></td>
						</tr>
					</table>
				</div>

				<div id='tab-profile'>
					<div class="vtabs">
						<span id="profile-add">
							<span><?= $button_add_profile; ?></span>
							<img src="<?= HTTP_THEME_IMAGE . 'add.png'; ?>" alt="" onclick="addProfile();"/>
						</span>
					</div>
					<div id="profiles"></div>
				</div>

			</form>
		</div>
	</div>
</div>


<div id="profile_tab_template" style="display:none">
	<a href="#tab-profile-%pid%" id="profile-%pid%">
		<span><?= $tab_profile . ' %pid%'; ?></span>
		<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" alt=""
		     onclick="$('.vtabs a:first').trigger('click'); $('#profile-%pid%').remove(); $('#tab-profile-%pid%').remove(); return false;"/>
	</a>
</div>

<div id="profile_template" style="display:none">
	<div id="tab-profile-%pid%" class="vtabs-content profiles">
		<table class="form">
			<tr>
				<td><?= $entry_store; ?></td>
				<td>
					<? $this->builder->set_config("store_id", "name"); ?>
					<?= $this->builder->build('multiselect', $data_stores, "profiles[%pid%][store_ids]"); ?>
				</td>
			</tr>
			<tr>
				<td><?= $entry_layout; ?></td>
				<td>
					<? $this->builder->set_config("layout_id", "name"); ?>
					<?= $this->builder->build('multiselect', $data_layouts, "profiles[%pid%][layout_ids]"); ?>
				</td>
			</tr>
			<tr>
				<td><?= $entry_position; ?></td>
				<td>
					<?= $this->builder->build('select', $data_positions, "profiles[%pid%][position]"); ?>
				</td>
			</tr>
			<tr>
				<td><?= $entry_profile_status; ?></td>
				<td>
					<?= $this->builder->build('select', $data_statuses, "profiles[%pid%][status]"); ?>
				</td>
			</tr>
		</table>
		<? if (!empty($extend_profile)) { ?>
			<div class="extend_profile">
				<?= $extend_profile; ?>
			</div>
		<? } ?>
	</div>
</div>

<script type="text/javascript">//<!--
	var profile_id = 0;

	<? foreach($profiles as $profile){ ?>
	data = {}
	<? foreach($profile as $key => $data) {?>
	data['<?= $key; ?>'] = <?= json_encode($data); ?>;
	<? } ?>

	addProfile(data);
	<? } ?>

	function addProfile(data) {
		tab_html = $('#profile_tab_template').html().replace(/%pid%/g, profile_id);
		$('#profile-add').before(tab_html);

		profile_html = $($('#profile_template').html().replace(/%pid%/g, profile_id));

		$('#profiles').append(profile_html);

		if (data) {
			fill_data($('#tab-profile-' + profile_id), data);
		}

		$('.vtabs a').tabs();

		$('#profile-' + profile_id).trigger('click');

		profile_id++;
	}

	function fill_data(context, data) {
		for (var d in data) {
			switch (d) {
				case 'status':
				case 'position':
					fill_data_as('select', context, d, data[d]);
					break;
				case 'layout_ids':
				case 'store_ids':
					fill_data_as('multiselect', context, d, data[d]);
					break;
				default:
					if (typeof user_fill_profile_data === 'function') {
						user_fill_profile_data(context, data);
					}
					break;
			}
		}
	}

	function fill_data_as(type, context, name, value) {
		switch (type) {
			case 'select':
				input = context.find('[name="profiles[' + profile_id + '][' + name + ']"]');
				input.val(value);
				break;

			case 'multiselect':
				for (var i = 0; i < value.length; i++) {
					input = context.find('[name="profiles[' + profile_id + '][' + name + '][]"][value=' + value[i] + ']');
					input.attr('checked', 'checked');
				}
				break;

			default:
				break;
		}
	}
//--></script>

<script type="text/javascript">//<!--
	$('#tabs a').tabs();
//--></script>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
