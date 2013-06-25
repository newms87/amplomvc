<?= $header; ?>
<div class="content">
	<?= $breadcrumbs; ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'information.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_name; ?></td>
						<td>
							<? foreach ($languages as $language) { ?>
								<input type="text" name="option_description[<?= $language['language_id']; ?>][name]" value="<?= isset($option_description[$language['language_id']]) ? $option_description[$language['language_id']]['name'] : ''; ?>" />
								<img src="<?= HTTP_THEME_IMAGE . "flags/$language[image]"; ?>" title="<?= $language['name']; ?>" /><br />
							<? } ?>
						</td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_display_name; ?></td>
						<td>
							<? foreach ($languages as $language) { ?>
								<input type="text" name="option_description[<?= $language['language_id']; ?>][display_name]" value="<?= isset($option_description[$language['language_id']]) ? $option_description[$language['language_id']]['display_name'] : ''; ?>" />
								<img src="<?= HTTP_THEME_IMAGE . "flags/$language[image]"; ?>" title="<?= $language['name']; ?>" /><br />
							<? } ?>
						</td>
					</tr>
					<tr>
						<td><?= $entry_type; ?></td>
						<td><?= $this->builder->build('select', $data_option_types, "type", $type); ?></td>
					</tr>
					<tr>
						<td><?= $entry_sort_order; ?></td>
						<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1" /></td>
					</tr>
				</table>
				<div style="padding:6px 0;">
					<a style="float:right;margin-right:10px;" onclick="image_manager();" class="button">File Manager</a>
					<div style="clear:both;"></div>
				</div>
				<table id="option-value" class="list">
					<thead>
						<tr>
							<td class="left"><span class="required"></span> <?= $entry_value; ?></td>
							<td class="left"><?= $entry_image; ?></td>
							<td class="right"><?= $entry_sort_order; ?></td>
							<td></td>
						</tr>
					</thead>
					<tbody>
						<? $option_value_row = 0; ?>
						<? foreach ($option_values as $option_value) { ?>
							<tr class="optionvaluerow" id="option-value-row<?= $option_value_row ?>">
								<td class="left">
										<input type="hidden" name="option_value[<?= $option_value_row; ?>][option_value_id]" value="<?= $option_value['option_value_id']; ?>" />
										<? foreach ($languages as $language) { ?>
										<input type="text" name="option_value[<?= $option_value_row; ?>][option_value_description][<?= $language['language_id']; ?>][name]" value="<?= isset($option_value['option_value_description'][$language['language_id']]) ? $option_value['option_value_description'][$language['language_id']]['name'] : ''; ?>" />
										<img src="<?= HTTP_THEME_IMAGE . "flags/$language[image]"; ?>" title="<?= $language['name']; ?>" /><br />
										<? if (isset($error_option_value[$option_value_row][$language['language_id']])) { ?>
										<span class="error"><?= $error_option_value[$option_value_row][$language['language_id']]; ?></span>
										<? } ?>
										<? } ?>
								</td>
								<td class="left"><div class="image"><img src="<?= $option_value['thumb']; ?>" alt="" id="thumb<?= $option_value_row; ?>" />
										<input type="hidden" name="option_value[<?= $option_value_row; ?>][image]" value="<?= $option_value['image']; ?>" id="image<?= $option_value_row; ?>"	/>
										<br />
										<a onclick="upload_images('image<?= $option_value_row; ?>', 'thumb<?= $option_value_row; ?>',<?= $option_value_row ?>);"><?= $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb<?= $option_value_row; ?>').attr('src', '<?= $no_image; ?>'); $('#image<?= $option_value_row; ?>').attr('value', '');"><?= $text_clear; ?></a></div></td>
								<td class="right"><input class="sortOrder" type="text" name="option_value[<?= $option_value_row; ?>][sort_order]" value="<?= $option_value['sort_order']; ?>" size="1" /></td>
								<td class="left"><a onclick="$('#option-value-row<?= $option_value_row; ?>').remove();" class="button"><?= $button_remove; ?></a></td>
							</tr>
						<? $option_value_row++; ?>
						<? } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="3"></td>
							<td class="left"><a onclick="addOptionValue();" class="button"><?= $button_add_option_value; ?></a></td>
						</tr>
					</tfoot>
				</table>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript"><!--
$('select[name=\'type\']').bind('change', function() {
	if (this.value == 'select' || this.value == 'radio' || this.value == 'checkbox' || this.value == 'image') {
		$('#option-value').show();
	} else {
		$('#option-value').hide();
	}
});

var option_value_row = <?= $option_value_row; ?>;

function addOptionValue() {
	html = '<tr class="optionvaluerow" id="option-value-row' + option_value_row + '">';
	html += '<td class="left"><input type="hidden" name="option_value[' + option_value_row + '][option_value_id]" value="" />';
	<? foreach ($languages as $language) { ?>
	html += '<input type="text" name="option_value[' + option_value_row + '][option_value_description][<?= $language['language_id']; ?>][name]" value="" /> <img src="<?= HTTP_THEME_IMAGE . "flags/$language[image]"; ?>" title="<?= $language['name']; ?>" /><br />';
	<? } ?>
	html += '</td>';
	html += '<td class="left"><div class="image"><img src="<?= $no_image; ?>" alt="" id="thumb' + option_value_row + '" /><input type="hidden" name="option_value[' + option_value_row + '][image]" value="" id="image' + option_value_row + '" /><br />';
	html += '<a onclick="upload_images(\'image' + option_value_row + '\', \'thumb' + option_value_row + '\',' + option_value_row + ');"><?= $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
	html += '<a onclick="$(\'#thumb' + option_value_row + '\').attr(\'src\', \'<?= $no_image; ?>\'); $(\'#image' + option_value_row + '\').attr(\'value\', \'\');"><?= $text_clear; ?></a></div></td>';
	html += '<td class="right"><input class="sortOrder" type="text" name="option_value[' + option_value_row + '][sort_order]" value="' + (option_value_row + 1) + '" size="1" /></td>';
	html += '<td class="left"><a onclick="$(\'#option-value-row' + option_value_row + '\').remove();" class="button"><?= $button_remove; ?></a></td>';
	html += '</tr>';
	
	$('#option-value tbody').append(html);
	
	option_value_row++;

	$('#option-value').sortable('refresh');
}
//--></script>

<?= $this->builder->js('errors',$errors); ?>

<script type="text/javascript">//<!--
var option_value_row = <?= $option_value_row; ?>;
function addImage(imageName) {
	html = '<tr class="optionvaluerow" id="option-value-row' + option_value_row + '">';
	html += '<td class="left"><input type="hidden" name="option_value[' + option_value_row + '][option_value_id]" value="" />';
	<? foreach ($languages as $language) { ?>
		html += '<input type="text" name="option_value[' + option_value_row + '][option_value_description][<?= $language['language_id']; ?>][name]" value="" /> <img src="<?= HTTP_THEME_IMAGE . "flags/$language[image]"; ?>" title="<?= $language['name']; ?>" /><br />';
	<? } ?>
	html += '</td>';
	html += '<td class="left"><div class="image"><img width="100" src="../image/' + imageName + '" alt="' + imageName + '" title="' + imageName + '" id="thumb' + option_value_row + '" /><input type="hidden" name="option_value[' + option_value_row + '][image]" value="' + imageName + '" id="image' + option_value_row + '" /><br /><a onclick="upload_images(\'image<?= $option_value_row; ?>\', \'thumb<?= $option_value_row; ?>\',<?= $option_value_row ?>);"><?= $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$(\'#thumb' + option_value_row + '\').attr(\'src\', \'<?= $no_image; ?>\'); $(\'#image' + option_value_row + '\').attr(\'value\', \'\');"><?= $text_clear; ?></a></div></td>';
	html += '<td class="right"><input class="sortOrder" type="text" name="option_value[' + option_value_row + '][sort_order]" value="' + (option_value_row + 1) + '" size="1" /></td>';
	html += '<td class="left"><a onclick="$(\'#option-value-row' + option_value_row + '\').remove();" class="button"><?= $button_remove; ?></a></td>';
	html += '</tr>';
	
	$('#option-value tbody').append(html);

	option_value_row++;
	
	$('#option-value').sortable('refresh');
};
	
$('#option-value').bind('sortupdate', function(event, ui) {
	var index = 0;
	$('#option-value tbody tr').each(function() {
			index += 1;
			var so = $(this).find('.sortOrder');
			so.val(index);
	});
});
		
$(document).ready(function() {
	var c = {};
	$('#option-value tbody').sortable({
			items: 'tr.optionvaluerow',
			forcePlaceholderSize:true,
			cursor: "move",
			helper: function(event) { return $('<div class="drag-row"><table></table></div>').find('table').append($(event.target).closest('tr').clone()).end(); },
			forceHelperSize: true,
			forcePlaceholderSize: true,
			scroll: true,
			scrollSensitivity: 30,
			scrollSpeed: 30
	});
	$('#option-value tbody').sortable('refresh');
});
//--></script>
<?= $footer; ?>