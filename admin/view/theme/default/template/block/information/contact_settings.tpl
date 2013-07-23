<table class="form">
	<tr>
		<td>
			<?= $entry_contact_info; ?>
		</td>
		<td>
			<span class="help"><?= $entry_contact_info_help; ?></span><br />
			<textarea name="settings[contact_info]" class="ckedit"><?= $settings['contact_info']; ?></textarea>
		</td>
	</tr>
</table>

<?= $this->builder->js('ckeditor'); ?>