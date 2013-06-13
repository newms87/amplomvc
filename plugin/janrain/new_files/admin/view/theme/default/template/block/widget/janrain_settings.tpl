<table class="form" id="janrain_table">
	<tr>
		<td><label class="required" for="application_domain"><?= $entry_application_domain; ?></label></td>
		<td>
			<input id="application_domain" type="text" name="settings[application_domain]" value="<?= $application_domain; ?>" />
			<label for="application_domain" class="janrain_label_desc"><?= $entry_application_domain_desc; ?></label>
		</td>
	</tr>
	<tr>
		<td><label class="required" for="api_key"><?= $entry_api_key; ?></label></td>
		<td>
			<input id="api_key" type="text" name="settings[api_key]" value="<?= $api_key; ?>" />
			<label for="api_key" class="janrain_label_desc"><?= $entry_api_key_desc; ?></label>
		</td>
	</tr>
	<tr>
		<td><label><?= $entry_display_icons; ?></label></td>
		<td>
			<div	class='display_icon_list'>
			<? foreach($data_display_icons as $key => $icon){ ?>
				<div class='display_icon_label'>
					<input id="icon-<?= $key; ?>" type='checkbox' name="settings[display_icons][]" value="<?= $key; ?>" <?= in_array($key, $display_icons) ? 'checked="checked"' : ''; ?> />
					<label for="icon-<?= $key; ?>" style="display: block">
						<div class='janrain-icon-small' style="background: url(<?= $social_icon_sprite; ?>) no-repeat 0 <?= $image_offset[$key]*-16; ?>px;"></div>
						<?= $icon; ?>
					</label>
				</div>
			<? }?>
			</div>
		</td>
	</tr>
	<tr>
		<td><label for="login_redirect"><?= $entry_login_redirect; ?></label></td>
		<td>
			<input id="login_redirect" type="text" name="settings[login_redirect]" value="<?= $login_redirect; ?>" />
			<label for="login_redirect" class="janrain_label_desc"><?= $entry_login_redirect_description; ?></label>
		</td>
	</tr>
	<tr>
		<td><label for="logout_redirect"><?= $entry_logout_redirect; ?></label></td>
		<td>
			<input id="logout_redirect" type="text" name="settings[logout_redirect]" value="<?= $logout_redirect; ?>" />
			<label for="logout_redirect" class="janrain_label_desc"><?= $entry_logout_redirect_description; ?></label>
		</td>
	</tr>
	<tr>
		<td><label for="integrate_header"><?= $entry_integrate_header; ?></label></td>
		<td>
			<?= $this->builder->build('select', $data_yes_no, 'settings[integrate_header]', $integrate_header); ?>
		</td>
	</tr>
</table>