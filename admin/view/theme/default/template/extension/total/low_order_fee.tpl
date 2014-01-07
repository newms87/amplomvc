<table class="form">
	<tr>
		<td><?= _l("Order Total:"); ?></td>
		<td><input type="text" name="settings[total]" value="<?= $settings['total']; ?>"/></td>
	</tr>
	<tr>
		<td><?= _l("Fee:"); ?></td>
		<td><input type="text" name="settings[fee]" value="<?= $settings['fee']; ?>"/></td>
	</tr>
	<tr>
		<td><?= _l("Tax Class:"); ?></td>
		<td>
			<? $this->builder->setConfig('tax_class_id', 'title'); ?>
			<?= $this->builder->build('select', $data_tax_classes, "settings[tax_class_id]", $settings['tax_class_id']); ?>
		</td>
	</tr>
</table>
