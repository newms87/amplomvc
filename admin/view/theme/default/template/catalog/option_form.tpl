<?= $header; ?>

<div class="section">
	<?= $this->breadcrumb->render(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'information.png'; ?>" alt=""/> <?= $head_title; ?></h1>

			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>

		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_name; ?></td>
						<td><input type="text" name="name" value="<?= $name; ?>"/></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_display_name; ?></td>
						<td><input type="text" name="display_name" value="<?= $display_name; ?>"/></td>
					</tr>
					<tr>
						<td><?= $entry_type; ?></td>
						<td><?= $this->builder->build('select', $data_option_types, "type", $type); ?></td>
					</tr>
					<tr>
						<td><?= $entry_sort_order; ?></td>
						<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1"/></td>
					</tr>
				</table>

				<table class="list">
					<thead>
					<tr>
						<td class="center required"><?= $entry_value; ?></td>
						<td class="center"><?= $entry_display_value; ?></td>
						<td class="center"><?= $entry_image; ?></td>
						<td class="center"><?= $entry_sort_order; ?></td>
						<td></td>
					</tr>
					</thead>
					<tbody id="option_value_list">
					<? foreach ($option_values as $row => $option_value) { ?>
						<tr class="optionvaluerow" data-row="<?= $row; ?>">
							<td class="center">
								<input type="hidden" name="option_value[<?= $row; ?>][option_value_id]" value="<?= $option_value['option_value_id']; ?>"/>
								<input type="text" name="option_value[<?= $row; ?>][value]" value="<?= $option_value['value']; ?>"/>
							</td>
							<td class="center">
								<input type="text" name="option_value[<?= $row; ?>][display_value]" size="50" value="<?= $option_value['display_value']; ?>"/>
							</td>
							<td class="center">
								<? $this->builder->setBuilderTemplate('click_image_small'); ?>
								<?= $this->builder->imageInput("option_value[$row][image]", $option_value['image'], null, null, $this->config->get('config_image_product_option_width'), $this->config->get('config_image_product_option_height')); ?>
							</td>
							<td class="center">
								<input class="sort_order" type="text" name="option_value[<?= $row; ?>][sort_order]" value="<?= $option_value['sort_order']; ?>" size="1"/>
							</td>

							<td class="center"><a onclick="$(this).closest('tr').remove();" class="button"><?= $button_remove; ?></a></td>
						</tr>
					<? } ?>
					</tbody>
					<tfoot>
					<tr>
						<td colspan="3"></td>
						<td class="left">
							<a onclick="add_option_value();" class="button"><?= $button_add_option_value; ?></a>
						</td>
					</tr>
					</tfoot>
				</table>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#option_value_list').ac_template('ov_list');

	function add_option_value() {
		$.ac_template('ov_list', 'add');
		$('#option_value_list').update_index('.sort_order');
	}

	$('#option_value_list').sortable({cursor: "move", stop: function () {
		$('#option_value_list').update_index();
	} });
</script>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
