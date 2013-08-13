<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'length.png'; ?>" alt="" /> <?= $head_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_title; ?></td>
						<td><? foreach ($languages as $language) { ?>
							<input type="text" name="length_class_description[<?= $language['language_id']; ?>][title]" value="<?= isset($length_class_description[$language['language_id']]) ? $length_class_description[$language['language_id']]['title'] : ''; ?>" />
							<img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /><br />
							<? if (isset($error_title[$language['language_id']])) { ?>
							<span class="error"><?= $error_title[$language['language_id']]; ?></span><br />
							<? } ?>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_unit; ?></td>
						<td><? foreach ($languages as $language) { ?>
							<input type="text" name="length_class_description[<?= $language['language_id']; ?>][unit]" value="<?= isset($length_class_description[$language['language_id']]) ? $length_class_description[$language['language_id']]['unit'] : ''; ?>" />
							<img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /><br />
							<? if (isset($error_unit[$language['language_id']])) { ?>
							<span class="error"><?= $error_unit[$language['language_id']]; ?></span><br />
							<? } ?>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= $entry_value; ?></td>
						<td><input type="text" name="value" value="<?= $value; ?>" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?>