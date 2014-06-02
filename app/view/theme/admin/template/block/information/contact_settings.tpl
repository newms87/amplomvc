<table class="form">
	<tr>
		<td>
			<?= _l("The Contact Page:"); ?>
		</td>
		<td>
			<span class="help"><?= _l("Write any HTML code / text for the Contact Page. Use %contact_form% to insert the contact form"); ?></span><br/>
			<textarea name="settings[contact_info]" class="ckedit"><?= $settings['contact_info']; ?></textarea>
		</td>
	</tr>
</table>

<?= build_js('ckeditor'); ?>
