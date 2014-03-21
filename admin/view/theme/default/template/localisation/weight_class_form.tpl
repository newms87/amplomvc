<?= $common_header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
		<div class="message warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'shipping.png'; ?>" alt=""/> <?= _l("Weight Class"); ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
					href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Weight Title:"); ?></td>
						<td><? foreach ($languages as $language) { ?>
								<input type="text" name="weight_class_description[<?= $language['language_id']; ?>][title]" value="<?= isset($weight_class_description[$language['language_id']]) ? $weight_class_description[$language['language_id']]['title'] : ''; ?>"/>
								<img src="<?= URL_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>"
									title="<?= $language['name']; ?>"/><br/>
								<? if (isset(_l("Weight Title must be between 3 and 32 characters!")[$language['language_id']])) { ?>
									<span class="error"><?= _l("Weight Title must be between 3 and 32 characters!")[$language['language_id']]; ?></span><br/>
								<? } ?>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Weight Unit:"); ?></td>
						<td><? foreach ($languages as $language) { ?>
								<input type="text" name="weight_class_description[<?= $language['language_id']; ?>][unit]" value="<?= isset($weight_class_description[$language['language_id']]) ? $weight_class_description[$language['language_id']]['unit'] : ''; ?>"/>
								<img src="<?= URL_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>"
									title="<?= $language['name']; ?>"/><br/>
								<? if (isset(_l("Weight Unit must be between 1 and 4 characters!")[$language['language_id']])) { ?>
									<span class="error"><?= _l("Weight Unit must be between 1 and 4 characters!")[$language['language_id']]; ?></span><br/>
								<? } ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= _l("Value:<br /><span class=\"help\">Set to 1.00000 if this is your default weight.</span>"); ?></td>
						<td><input type="text" name="value" value="<?= $value; ?>"/></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $common_footer; ?>
