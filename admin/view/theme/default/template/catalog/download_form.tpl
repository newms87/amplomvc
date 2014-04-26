<?= _call('common/header'); ?>
<div class="section">
	<?= _breadcrumbs(); ?>
	<? if ($error_warning) { ?>
		<div class="message warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'download.png'; ?>" alt=""/> <?= _l("Downloads"); ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
					href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Download Name:"); ?></td>
						<td><? foreach ($languages as $language) { ?>
								<input type="text" name="download_description[<?= $language['language_id']; ?>][name]" value="<?= isset($download_description[$language['language_id']]) ? $download_description[$language['language_id']]['name'] : ''; ?>"/>
								<img src="<?= URL_THEME_IMAGE . "flags/$language[image]"; ?>" title="<?= $language['name']; ?>"/><br/>
								<? if (isset(_l("Name must be between 3 and 64 characters!")[$language['language_id']])) { ?>
									<span class="error"><?= _l("Name must be between 3 and 64 characters!")[$language['language_id']]; ?></span><br/>
								<? } ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= _l("Filename:"); ?></td>
						<td><input type="file" name="download" value=""/>
							<br/>
							<span class="help"><?= $filename; ?></span>
						</td>
					</tr>
					<tr>
						<td><?= _l("Total Downloads Allowed:"); ?></td>
						<td><input type="text" name="remaining" value="<?= $remaining; ?>" size="6"/></td>
					</tr>
					<? if ($show_update) { ?>
						<tr>
							<td>
								<div><?= _l("Push to Previous Customers:"); ?></div>
								<span class="help"><?= _l("Check this to update previously purchased versions as well."); ?></span>
							</td>
							<td><? if ($update) { ?>
									<input type="checkbox" name="update" value="1" checked="checked"/>
								<? } else { ?>
									<input type="checkbox" name="update" value="1"/>
								<? } ?></td>
						</tr>
					<? } ?>
				</table>
			</form>
		</div>
	</div>
</div>
<?= _call('common/footer'); ?>
