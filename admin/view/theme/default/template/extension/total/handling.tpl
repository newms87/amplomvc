<table class="form">
	<tr>
		<td><?= $entry_total; ?></td>
		<td><input type="text" name="settings[total]" value="<?= $settings['total']; ?>"/></td>
	</tr>
	<tr>
		<td><?= $entry_fee; ?></td>
		<td><input type="text" name="settings[fee]" value="<?= $settings['fee']; ?>"/></td>
	</tr>
	<tr>
		<td><?= $entry_tax_class; ?></td>
		<td>
			<? $this->builder->setConfig('tax_class_id', 'title'); ?>
			<?= $this->builder->build('select', $data_tax_classes, "settings[tax_class_id]", $settings['tax_class_id']); ?>
		</td>
	</tr>
</table>
