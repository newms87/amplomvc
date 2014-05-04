<?= _call('common/header'); ?>
<div class="section">
	<?= _breadcrumbs(); ?>
	<? if ($error_warning) { ?>
		<div class="message warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/length.png'); ?>" alt=""/> <?= _l("Length Class"); ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
					href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Length Title:"); ?></td>
						<td><? foreach ($languages as $language) { ?>
								<input type="text" name="length_class_description[<?= $language['language_id']; ?>][title]" value="<?= isset($length_class_description[$language['language_id']]) ? $length_class_description[$language['language_id']]['title'] : ''; ?>"/>
								<img src="<?= theme_url('image/flags/<?= $language[')mage']; ?>'; ?>"
									title="<?= $language['name']; ?>"/><br/>
								<? if (isset(_l("Length Title must be between 3 and 32 characters!")[$language['language_id']])) { ?>
									<span class="error"><?= _l("Length Title must be between 3 and 32 characters!")[$language['language_id']]; ?></span><br/>
								<? } ?>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Length Unit:"); ?></td>
						<td><? foreach ($languages as $language) { ?>
								<input type="text" name="length_class_description[<?= $language['language_id']; ?>][unit]" value="<?= isset($length_class_description[$language['language_id']]) ? $length_class_description[$language['language_id']]['unit'] : ''; ?>"/>
								<img src="<?= theme_url('image/flags/<?= $language[')image']; ?>'; ?>"
									title="<?= $language['name']; ?>"/><br/>
								<? if (isset(_l("Length Unit must be between 1 and 4 characters!")[$language['language_id']])) { ?>
									<span class="error"><?= _l("Length Unit must be between 1 and 4 characters!")[$language['language_id']]; ?></span><br/>
								<? } ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= _l("Value:<br /><span class=\"help\">Set to 1.00000 if this is your default length.</span>"); ?></td>
						<td><input type="text" name="value" value="<?= $value; ?>"/></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= _call('common/footer'); ?>
