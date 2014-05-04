<?= _call('common/header'); ?>
<div class="section">
	<?= _breadcrumbs(); ?>
	<form action="<?= $save; ?>" method="post" class="box">
		<div class="heading">
			<h1><img class="icon" src="<?= theme_url('image/login_settings.png'); ?>" alt=""/> <?= _l("Login Settings"); ?></h1>

			<div class="buttons">
				<button class="button"><?= _l("Save"); ?></button>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>

		<div class="section">
			<table class="form">
				<tr>
					<td><?= _l("Google Login"); ?></td>
					<td>
						<div class="setting-item">
							<label for="google-application-name"><?= _l("Application Name"); ?></label>
							<input id="google-application-name" class="long" type="text" name="google_plus[application_name]" value="<?= $google_plus['application_name']; ?>" />
						</div>
						<div class="setting-item">
							<label for="google-api-key"><?= _l("API Key"); ?></label>
							<input id="google-api-key" class="long" type="text" name="google_plus[api_key]" value="<?= $google_plus['api_key']; ?>" />
						</div>
						<div class="setting-item">
							<label for="google-client-id"><?= _l("Client ID"); ?></label>
							<input id="google-client-id" class="long" type="text" name="google_plus[client_id]" value="<?= $google_plus['client_id']; ?>" />
						</div>
						<div class="setting-item">
							<label for="google-client-secret"><?= _l("Client Secret"); ?></label>
							<input id="google-client-secret" class="long" type="text" name="google_plus[client_secret]" value="<?= $google_plus['client_secret']; ?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<?= _l("Facebook Login"); ?>
						<span class="help"><?= _l("(NOTE: If you enter App ID incorrectly, facebook will stop at a whitescreen during login.)"); ?></span>
					</td>
					<td>
						<div class="setting-item">
							<label for="fb-app-id"><?= _l("App ID"); ?></label>
							<input id="fb-app-secret" class="long" type="text" name="facebook[app_id]" value="<?= $facebook['app_id']; ?>" />
						</div>
						<div class="setting-item">
							<label for="fb-app-secret"><?= _l("App Secret"); ?></label>
							<input id="fb-app-secret" class="long" type="text" name="facebook[app_secret]" value="<?= $facebook['app_secret']; ?>" />
						</div>
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= _call('common/footer'); ?>
