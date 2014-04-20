<?= $this->call('common/header'); ?>

<div class="section">
	<?= $this->breadcrumb->render(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'information.png'; ?>" alt=""/> <?= _l("Options"); ?></h1>

			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>

		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Option Name:"); ?></td>
						<td><input type="text" name="name" value="<?= $name; ?>"/></td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Option Display Name:"); ?></td>
						<td><input type="text" name="display_name" value="<?= $display_name; ?>"/></td>
					</tr>
					<tr>
						<td><?= _l("Type:"); ?></td>
						<td><?= $this->builder->build('select', $data_option_types, "type", $type); ?></td>
					</tr>
					<tr>
						<td><?= _l("Sort Order:"); ?></td>
						<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1"/></td>
					</tr>
				</table>

				<table class="list">
					<thead>
						<tr>
							<td class="center required"><?= _l("Option Value:"); ?></td>
							<td class="center"><?= _l("Option Value Display:<span class=\"help\">The value to show to customers.<br/>Leave blank to use Option Value. HTML enabled.</span>"); ?></td>
							<td class="center"><?= _l("Image:"); ?></td>
							<td class="center"><?= _l("Sort Order:"); ?></td>
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
									<input type="text" class="imageinput" name="option_value[<?= $row; ?>][image]" value="<?= $option_value['image']; ?>" data-thumb="<?= $option_value['thumb']; ?>"/>
								</td>
								<td class="center">
									<input class="sort_order" type="text" name="option_value[<?= $row; ?>][sort_order]" value="<?= $option_value['sort_order']; ?>" size="1"/>
								</td>

								<td class="center"><a onclick="$(this).closest('tr').remove();" class="button"><?= _l("Remove"); ?></a></td>
							</tr>
						<? } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="3"></td>
							<td class="left">
								<a onclick="add_option_value();" class="button"><?= _l("Add Option Value"); ?></a>
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
		var ov = $.ac_template('ov_list', 'add');
		ov.find('.imageinput').ac_imageinput();
		$('#option_value_list').update_index('.sort_order');
	}

	$('#option_value_list').sortable({cursor: "move", stop: function () {
		$('#option_value_list').update_index();
	} });

	$('.imageinput').ac_imageinput();
</script>

<?= $this->builder->js('errors', $errors); ?>

<?= $this->call('common/footer'); ?>
