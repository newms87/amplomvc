<table class="form">
	<tr>
		<td>
			<?= $entry_attribute_group; ?><br/>
			<? $this->builder->set_config("attribute_group_id", 'name'); ?>
			<?= $this->builder->build('select', $data_attribute_groups, 'attribute_group_select'); ?>
		</td>
		<td>
			<div id="attribute_list">

				<? $settings['attributes']['template_row'] = array(
					'attribute_group_id' => "%attribute_group_id%",
					'group_name'         => "%group_name%",
					'menu_name'          => "%menu_name%",
				); ?>

				<? foreach ($settings['attributes'] as $key => $attribute) { ?>
					<? $row = $attribute['attribute_group_id']; ?>
					<div class="attribute <?= $key; ?>">
						<input type="hidden" name="settings[attributes][<?= $row; ?>][attribute_group_id]" value="<?= $attribute['attribute_group_id']; ?>"/>
						<label for="attribute_group_name<?= $key; ?>"><?= $entry_attribute_group_name; ?></label>
						<input id="attribute_group_name<?= $key; ?>" type="text" name="settings[attributes][<?= $row; ?>][group_name]" value="<?= $attribute['group_name']; ?>"/>
						<label for="attribute_menu_name<?= $key; ?>"><?= $entry_attribute_menu_name; ?></label>
						<input id="attribute_menu_name<?= $key; ?>" type="text" name="settings[attributes][<?= $row; ?>][menu_name]" value="<?= $attribute['menu_name']; ?>"/>
						<a class="delete" onclick="$(this).closest('.attribute').remove()"><?= $button_delete; ?></a>
					</div>
				<? } ?>
			</div>
		</td>
	</tr>
	<? /*TODO:
	*
	*Add block that allows user to select page (can be used for navigation and elsewhere)
	*
	* This block will allows users to choose new custom URL, a category, a product, a page, an information page, etc..
	*/
	?>
	<tr>
		<td>
			<?= $entry_page_link; ?>
		</td>
		<td>COMING SOON!</td>
	</tr>
</table>

<script type="text/javascript">//<!--
	var list_template = $('#attribute_list').find('.template_row');
	var attributes_template = list_template.html();
	list_template.remove();

	$('[name=attribute_group_select]').change(function () {
		if ($('#attribute_list .attribute.' + $(this).val()).length) return '';

		option = $(this).find('option[value="' + $(this).val() + '"]');

		template = attributes_template
			.replace(/%attribute_group_id%/g, $(this).val())
			.replace(/%group_name%/g, option.html())
			.replace(/%menu_name%/g, option.html());

		$('#attribute_list').append($('<div class="attribute ' + $(this).val() + '" />').append(template));
	});
//--></script>
