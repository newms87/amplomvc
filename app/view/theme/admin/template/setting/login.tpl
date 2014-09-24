<?= IS_AJAX ? '' : call('admin/common/header'); ?>
<div class="section">
	<?= IS_AJAX ? '' : breadcrumbs(); ?>
	<form action="<?= $save; ?>" method="post" class="box">
		<div class="heading">
			<h1>
				<img class="icon" src="<?= theme_url('image/settings/login.png'); ?>" alt=""/> <?= _l("Login Settings"); ?>
			</h1>

			<div class="buttons">
				<button class="button"><?= _l("Save"); ?></button>
				<a href="<?= site_url('admin/setting/store'); ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>

		<div class="section">
			<table class="form">
				<tr>
					<td><?= _l("Enable Social Media Login / Registration?"); ?></td>
					<td><?=
						build('ac-radio', array(
							'name'   => 'status',
							'data'   => $data_yes_no,
							'select' => $status,
						)); ?>
					</td>
				</tr>
			</table>

			<table class="form social-media-list">
				<tr class="social-media google-plus">
					<td>
						<div class="label"><?= _l("Google Login"); ?></div>
						<div class="help">
							<a target="_blank" href="https://developers.google.com/+/web/signin/server-side-flow"><?= _l("Learn to setup with Google+ API"); ?></a>
							<br/><br/>
							<span class="step step1"><?= _l("Create a Web application and use %s for the Authorized Redirect URI", store_url(1, "block/login/google/connect")); ?> </span>
						</div>
					</td>
					<td>
						<div class="setting-item enable-item">
							<?=
							build('ac-radio', array(
								'name'   => 'google_plus[active]',
								'data'   => $data_active,
								'select' => $google_plus['active'],
							)); ?>
						</div>
						<div class="setting-item">
							<label for="google-application-name"><?= _l("Application Name"); ?></label>
							<input id="google-application-name" class="long" type="text" name="google_plus[application_name]" value="<?= $google_plus['application_name']; ?>"/>
						</div>
						<div class="setting-item">
							<label for="google-api-key"><?= _l("API Key"); ?></label>
							<input id="google-api-key" class="long" type="text" name="google_plus[api_key]" value="<?= $google_plus['api_key']; ?>"/>
						</div>
						<div class="setting-item">
							<label for="google-client-id"><?= _l("Client ID"); ?></label>
							<input id="google-client-id" class="long" type="text" name="google_plus[client_id]" value="<?= $google_plus['client_id']; ?>"/>
						</div>
						<div class="setting-item">
							<label for="google-client-secret"><?= _l("Client Secret"); ?></label>
							<input id="google-client-secret" class="long" type="text" name="google_plus[client_secret]" value="<?= $google_plus['client_secret']; ?>"/>
						</div>
					</td>
				</tr>
				<tr class="social-media facebook">
					<td>
						<?= _l("Facebook Login"); ?>
						<span class="help"><?= _l("Go to <a target=\"_blank\" href=\"%s\">Facebook Developer Console</a> to acquire your API Key and Secret. <a target=\"_blank\" href=\"%s\">Learn Here if you need help</a>", 'https://developers.facebook.com', 'https://developers.facebook.com/docs/facebook-login/login-flow-for-web/v2.0'); ?> </span>
						<span class="help"><?= _l("(NOTE: If you enter App ID incorrectly, facebook will stop at a whitescreen during login.)"); ?></span>
					</td>
					<td>
						<div class="setting-item enable-item">
							<?=
							build('ac-radio', array(
								'name'   => 'facebook[active]',
								'data'   => $data_active,
								'select' => $facebook['active'],
							)); ?>
						</div>
						<div class="setting-item">
							<label for="fb-app-id"><?= _l("App ID"); ?></label>
							<input id="fb-app-secret" class="long" type="text" name="facebook[app_id]" value="<?= $facebook['app_id']; ?>"/>
						</div>
						<div class="setting-item">
							<label for="fb-app-secret"><?= _l("App Secret"); ?></label>
							<input id="fb-app-secret" class="long" type="text" name="facebook[app_secret]" value="<?= $facebook['app_secret']; ?>"/>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>

<script type="text/javascript">
	$('[name=status]').change(function () {
		var val = $(this).closest('tr').find(':checked').val();
		$('.social-media-list').toggleClass('hide', val != 1);
	}).change();

	$('.enable-item input').change(function() {
		var val = $(this).closest('tr').find(':checked').val();
		$(this).closest('tr').find('.setting-item').not('.enable-item').toggleClass('hide', val != 1);
	}).change();

	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= IS_AJAX ? '' : call('admin/common/footer'); ?>
