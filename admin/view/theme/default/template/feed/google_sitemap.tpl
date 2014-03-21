<?= $common_header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<? if ($error_warning) { ?>
			<div class="message warning"><?= $error_warning; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= URL_THEME_IMAGE . 'feed.png'; ?>" alt=""/> <?= _l("Google Sitemap"); ?></h1>

				<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
						href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
			</div>
			<div class="section">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td><?= _l("Status:"); ?></td>
							<td><select name="google_sitemap_status">
									<? if ($google_sitemap_status) { ?>
										<option value="1" selected="selected"><?= _l("Enabled"); ?></option>
										<option value="0"><?= _l("Disabled"); ?></option>
									<? } else { ?>
										<option value="1"><?= _l("Enabled"); ?></option>
										<option value="0" selected="selected"><?= _l("Disabled"); ?></option>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= _l("Data Feed Url:"); ?></td>
							<td><textarea cols="40" rows="5"><?= $data_feed; ?></textarea></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
<?= $common_footer; ?>
