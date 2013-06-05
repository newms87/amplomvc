<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'product.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><? if($editable){?><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><? }?><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<? if(!$editable){?>
				<span><?= $text_not_editable; ?></span>
			<? }else{?>
			<div id="tabs" class="htabs"><a href="#tab-general"><?= $tab_general; ?></a><a href='#tab-option'><?= $tab_option; ?></a><a href="#tab-image"><?= $tab_image; ?></a><a href="#tab-shipping"><?= $tab_shipping; ?></a></div>
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<? foreach ($languages as $language) { ?>
					<div id="language<?= $language['language_id']; ?>">
						<table class="form">
							<tr>
								<td class="required"> <?= $entry_name; ?></td>
								<td><input type="text" name="product_description[<?= $language['language_id']; ?>][name]" size="100" value="<?= isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['name'] : ''; ?>" /></td>
							</tr>
							<tr style='display:none'>
								<td><?= $entry_quantity; ?></td>
								<td><input type="hidden" name="quantity" value="<?= $quantity; ?>" size="2" /></td>
							</tr>
							<tr>
								<td><?= $entry_blurb; ?></td>
								<td><textarea style='width:30%;height:150px' name="product_description[<?= $language['language_id']; ?>][blurb]" id="blurb<?= $language['language_id']; ?>"><?= isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['blurb'] : ''; ?></textarea></td>
							</tr>
							<tr>
								<td><?= $entry_description; ?></td>
								<td><textarea name="product_description[<?= $language['language_id']; ?>][description]" class='ckedit' id="description<?= $language['language_id']; ?>"><?= isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['description'] : ''; ?></textarea></td>
							</tr>
						</table>
					</div>
					<? } ?>
					<table class="form">
						<tr>
							<td><?= $entry_model; ?></td>
							<td>
								<input type="text" name="model" value="<?= $model; ?>" />
								<a class='gen_url' onclick='generate_model(this)'><?= $button_generate_model; ?></a>
							</td>
						</tr>
						<tr>
							<td><?= $entry_manufacturer; ?></td>
							<td>
								<? if(count($manufacturers) == 1){?>
										<div class='restrict_man_name'><input type='hidden' name='manufacturer_id' value='<?= key($manufacturers); ?>' /> <?= array_pop($manufacturers); ?></div>
								<? } elseif(count($manufacturers) > 1){?>
										<?= $this->builder->build('select',$manufacturers,'manufacturer_id',$manufacturer_id); ?>
								<? }?>
							</td>
						</tr>
						<tr>
							<td><?= $entry_image; ?></td>
								<td><div class="image"><img src="<?= $thumb; ?>" alt="" id="thumb" /><br />
								<input type="hidden" name="image" value="<?= $image; ?>" id="image" />
								<a onclick="upload_image('image','thumb');"><?= $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
								<a onclick="$('#thumb').attr('src', '<?= $no_image; ?>'); $('#image').attr('value', '');"><?= $text_clear; ?></a></div>
							</td>
						</tr>
					</table>
				</div>
				<div id="tab-option">
					<div id="vtab-option" class="vtabs">
						<? foreach ($product_options as $product_option_id=>$product_option) { ?>
						<? $option_id = $product_option['option_id'];?>
						<a href="#tab-option-<?= $option_id; ?>" id="option-<?= $option_id; ?>">
							<?= $product_option['name']; ?>
							<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" alt="" onclick="$('#vtabs a:first').trigger('click'); $('#option-<?= $option_id; ?>').remove(); $('#tab-option-<?= $option_id; ?>').remove(); update_ov_entries_select(); return false;" />
						</a>
						<? } ?>
						<span id="option-add">
							<input value="" style="width: 130px;" />
							<img src="<?= HTTP_THEME_IMAGE . 'add.png'; ?>" alt="<?= $button_add_option; ?>" title="<?= $button_add_option; ?>" />
						</span>
						<div class='help'><?= $text_option_help; ?></div>
					</div>
					<? foreach ($product_options as $product_option) { ?>
						<? $option_id = $product_option['option_id'];?>
					<div id="tab-option-<?= $option_id; ?>" class="vtabs-content">
						<input type="hidden" name="product_options[<?= $option_id; ?>][product_option_id]" value="<?= $product_option_id; ?>" />
						<input type="hidden" name="product_options[<?= $option_id; ?>][name]" value="<?= $product_option['name']; ?>" />
						<input type="hidden" name="product_options[<?= $option_id; ?>][option_id]" value="<?= $product_option['option_id']; ?>" />
						<input type="hidden" name="product_options[<?= $option_id; ?>][type]" value="<?= $product_option['type']; ?>" />
						<table class="form">
							<tr>
								<td><?= $entry_required; ?></td>
								<td><?= $this->builder->build('select', $data_yes_no, "product_options[$option_id][required]", (int)$product_option['required']); ?></td>
							</tr>
							<tr>
								<td><?= $entry_sort_order; ?></td>
								<td><input type="text" name="product_options[<?= $option_id; ?>][sort_order]" value="<?= $product_option['sort_order']; ?>" /></td>
							</tr>
							<tr>
								<td><?= $entry_option_value_list; ?></td>
								<td>
										<div class='scrollbox option_value_list clickable'>
										<? foreach($unused_option_values[$option_id] as $ov) {?>
												<div onclick="addOptionValue(<?= $option_id; ?>,<?= $ov['option_value_id']; ?>,'<?= addslashes($ov['name']); ?>'); $(this).remove();" ><span class='po_label'><?= $ov['name']; ?></span><img src='<?= HTTP_THEME_IMAGE . 'add.png' /></div>
										<? }?>
										</div>
								</td>
							</tr>
						</table>
						<table id='; ?>"option-value<?= $option_id; ?>" class="list">
							<thead>
								<tr>
									<td class="left"><?= $entry_option_value; ?></td>
									<td class="right"><?= $entry_quantity; ?></td>
									<td class="left"><?= $entry_subtract; ?></td>
									<td class="right"><?= $entry_cost; ?></td>
									<td class="right"><?= $entry_price; ?></td>
									<td class="right"><?= $entry_option_points; ?></td>
									<td class="right"><?= $entry_weight; ?></td>
									<td class="center"><?= $entry_option_value_restriction; ?></td>
									<td></td>
								</tr>
							</thead>
							<tbody class='option_value_entries'>
							<? foreach ($product_option['product_option_value'] as $product_option_value_id=>$product_option_value) { ?>
								<? $option_value_id = $product_option_value['option_value_id'];?>
								<tr>
									<td class="left">
										<span class='option_value_label'><?= $product_option_value['name']; ?></span>
										<input class="ov_entry_name" type="hidden" name="product_options[<?= $option_id; ?>][product_option_value][<?= $option_value_id; ?>][name]" value="<?= $product_option_value['name']; ?>" />
										<input type="hidden" name="product_options[<?= $option_id; ?>][product_option_value][<?= $option_value_id; ?>][option_id]" value="<?= $option_id; ?>" />
										<input class="ov_entry_option_value_id" type="hidden" name="product_options[<?= $option_id; ?>][product_option_value][<?= $option_value_id; ?>][option_value_id]" value="<?= $option_value_id; ?>" />
									</td>
									<td class="right"><input type="text" name="product_options[<?= $option_id; ?>][product_option_value][<?= $option_value_id; ?>][quantity]" value="<?= $product_option_value['quantity']; ?>" size="3" /></td>
									<td class="left">
										<?= $this->builder->build('select', $data_yes_no, "product_options[$option_id][product_option_value][$option_value_id][subtract]", (int)$product_option_value['subtract']); ?>
									</td>
									<td class="right">
										<input type="text" name="product_options[<?= $option_id; ?>][product_option_value][<?= $option_value_id; ?>][cost]" value="<?= $product_option_value['cost']; ?>" size="5" />
									</td>
									<td class="right">
										<input type="text" name="product_options[<?= $option_id; ?>][product_option_value][<?= $option_value_id; ?>][price]" value="<?= $product_option_value['price']; ?>" size="5" />
									</td>
									<td class="right">
										<input type="text" name="product_options[<?= $option_id; ?>][product_option_value][<?= $option_value_id; ?>][points]" value="<?= $product_option_value['points']; ?>" size="5" />
									</td>
									<td class="right">
										<input type="text" name="product_options[<?= $option_id; ?>][product_option_value][<?= $option_value_id; ?>][weight]" value="<?= $product_option_value['weight']; ?>" size="5" />
									</td>
									<td class="center">
										<table class='list'>
												<thead>
													<tr>
															<td class="center"><?= $entry_restriction_option_value; ?></td>
															<td class="center"><?= $entry_restriction_quantity; ?></td>
															<td></td>
													</tr>
												</thead>
												<tbody class='restrict_entries'>
													<? if(isset($product_option_value['restrictions'])){?>
													<? foreach($product_option_value['restrictions'] as $row=>$restriction){?>
													<tr>
															<?= $this->builder->set_config('option_value_id', 'name'); ?>
															<td class="center"><?= $this->builder->build('select', $all_product_option_values, "product_options[$option_id][product_option_value][$option_value_id][restrictions][$row][restrict_option_value_id]", $restriction['restrict_option_value_id'], array('class'=>'restrict_option_values')); ?></td>
															<td class="center"><input type="text" size='3' name="product_options[<?= $option_id; ?>][product_option_value][<?= $option_value_id; ?>][restrictions][<?= $row; ?>][quantity]" value="<?= $restriction['quantity']; ?>" /></td>
															<td class="center"><a onclick="$(this).closest('tr').remove()" class="button_remove"></a></td>
													</tr>
													<? }?>
													<? }?>
												</tbody>
												<tfoot>
													<tr>
															<td colspan='2'></td>
															<td class="center"><a onclick="add_restriction_value($(this), <?= $option_id; ?>, <?= $option_value_id; ?>)" class="button_add"></a></td>
													</tr>
												</tfoot>
										</table>
									</td>
									<td class="left">
										<a onclick="remove_option_value($(this), <?= $option_id; ?>, <?= $option_value_id; ?>, '<?= addslashes($product_option_value['name']); ?>');" class="button"><?= $button_remove; ?></a>
									</td>
								</tr>
							<? } ?>
							</tbody>
						</table>
					</div>
					<? } ?>
				</div>
				<div id="tab-image">
					<div style="padding:6px 0;">
						<a style="float:right;margin-right:10px;" onclick="image_manager();" class="button">File Manager</a>
						<div style="clear:both;"></div>
					</div>
					<table id="images" class="list">
						<thead>
							<tr>
								<td class="left"><?= $entry_image; ?></td>
								<td class="left"><?= $entry_filename; ?></td>
								<td class="right"><?= $entry_primary; ?></td>
								<td class="right"><?= $entry_sort_order; ?></td>
								<td></td>
							</tr>
						</thead>
						<tbody>
							<? $image_row = 0; ?>
							<? foreach ($product_images as $product_image) { ?>
							<? $image_selected = false; ?>
							<?
									if($image == $product_image['image'])
											$image_selected = true;
							?>
							<tr class="imagerow" id="image-row<?= $image_row; ?>">
								<td class="left"><div class="image"><img src="<?= $product_image['thumb']; ?>" alt="<?= $product_image['image']; ?>" title="<?= $product_image['image']; ?>" id="thumb<?= $image_row; ?>" />
										<input type="hidden" name="product_images[<?= $image_row; ?>][image]" value="<?= $product_image['image']; ?>" id="image<?= $image_row; ?>" />
										<br /><a onclick="upload_image('image<?= $image_row; ?>','thumb<?= $image_row; ?>',<?= $image_row; ?>);"><?= $text_browse; ?></a>
								</div></td>
								<td class="left"><?= $product_image['image']; ?></td>
								<td class="right"><input type="radio" name="primary_product_image" <?
																																												if ($image_selected == true)
																																												echo 'checked=checked'
																																										?>
										value="<?= $product_image['image']; ?>" /></td>
								<td class="right"><input class="sortOrder" type="text" name="product_images[<?= $image_row; ?>][sort_order]" value="<?= $product_image['sort_order']; ?>" size="2" /></td>
								<td class="left"><a onclick="$('#image-row<?= $image_row; ?>').remove();" class="button"><?= $button_remove; ?></a></td>
							</tr>
						
						<? $image_row++; ?>
						<? } ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="3"></td>
								<td class="left"><a onclick="image_manager();" class="button">File Manager</a>
										</td>
							</tr>
						</tfoot>
					</table>
				</div>
				<div id='tab-shipping'>
				<table class="form">
					<tr>
						<td><?= $entry_shipping_ret; ?></td>
						<td>
								<div id='ship_autofill'>
									<span class='help'><?= $text_ship_autofill; ?></span>
									<? foreach($manufacturers as $mid=>$m){?><a class='fill_ship' onclick='fill_shipping_return_policy(this,<?= $mid; ?>)'><?= sprintf($button_fill_ship,$m); ?></a><? }?>
								</div>
								<? foreach($languages as $language){?>
								<textarea name="product_description[<?= $language['language_id']; ?>][shipping_return]" cols="40" rows="5" id='shipping_return<?= $language['language_id']; ?>' class='ckedit'><?= isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['shipping_return'] : ''; ?></textarea>
								<? }?>
						</td>
					</tr>
					<tr>
							<td><?= $entry_dimension; ?></td>
							<td><input type="text" name="length" value="<?= $length; ?>" size="4" />
								<input type="text" name="width" value="<?= $width; ?>" size="4" />
								<input type="text" name="height" value="<?= $height; ?>" size="4" /></td>
						</tr>
						<tr>
							<td><?= $entry_length; ?></td>
							<td><select name="length_class_id">
									<? foreach ($length_classes as $length_class) { ?>
									<? if ($length_class['length_class_id'] == $length_class_id) { ?>
									<option value="<?= $length_class['length_class_id']; ?>" selected="selected"><?= $length_class['title']; ?></option>
									<? } else { ?>
									<option value="<?= $length_class['length_class_id']; ?>"><?= $length_class['title']; ?></option>
									<? } ?>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= $entry_weight; ?></td>
							<td><input type="text" name="weight" value="<?= $weight; ?>" /></td>
						</tr>
						<tr>
							<td><?= $entry_weight_class; ?></td>
							<td><select name="weight_class_id">
									<? foreach ($weight_classes as $weight_class) { ?>
									<? if ($weight_class['weight_class_id'] == $weight_class_id) { ?>
									<option value="<?= $weight_class['weight_class_id']; ?>" selected="selected"><?= $weight_class['title']; ?></option>
									<? } else { ?>
									<option value="<?= $weight_class['weight_class_id']; ?>"><?= $weight_class['title']; ?></option>
									<? } ?>
									<? } ?>
								</select></td>
						</tr>
					</table>
				</div>
				<div style='display:none'>
					<? foreach($languages as $language){?>
					<textarea name="product_description[<?= $language['language_id']; ?>][meta_description]" cols="40" rows="5"><?= isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
					<textarea name="product_description[<?= $language['language_id']; ?>][meta_keyword]" cols="40" rows="5"><?= isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
					<input type="text" name="product_tag[<?= $language['language_id']; ?>]" value="<?= isset($product_tag[$language['language_id']]) ? $product_tag[$language['language_id']] : ''; ?>" size="80" />
					<? }?>
				</div>
			</form>
			<? }?>
		</div>
	</div>
</div>
<?= $this->builder->js('ckeditor'); ?>

<script type="text/javascript">//<!--

$.widget('custom.catcomplete', $.ui.autocomplete, {
	_renderMenu: function(ul, items) {
			var self = this, currentCategory = '';
			
			$.each(items, function(index, item) {
				if (item.category != currentCategory) {
						ul.append('<li class="ui-autocomplete-category">' + item.category + '</li>');
						
						currentCategory = item.category;
				}
				
				self._renderItem(ul, item);
			});
	}
});

//--></script>

<?= $this->builder->js('html_entity_decode'); ?>

<script type="text/javascript">//<!--
function fill_shipping_return_policy(c,id){
	$(c).fadeOut(500,function(){$(c).show();});
	$.post("<?= HTTP_ADMIN . "index.php?route=catalog/product/fill_shipping_return_policy"; ?>",{manufacturer_id:id},
	function(json){
			<? foreach ($languages as $language) { ?>
				remove_ckeditor_for('shipping_return<?= $language['language_id']; ?>');
				$('#shipping_return<?= $language['language_id']; ?>').val(html_entity_decode(json[<?= $language['language_id']; ?>]['shipping_return']));
				init_ckeditor_for('shipping_return<?= $language['language_id']; ?>');
			<? } ?>
	},'json');
}
//--></script>
<script type="text/javascript">//<!--
function generate_model(c){
	$(c).fadeOut(500,function(){$(c).show();});
	name = $('input[name="product_description[1][name]"]').val();
	if(!name)
			alert("Please make a name for this product before generating the Model ID");
	$.post("<?= HTTP_ADMIN . "index.php?route=catalog/product/generate_model"; ?>",{name:name},function(json){$('input[name="model"]').val(json);},'json');
}
//--></script>

<script type="text/javascript"><!--
$('#option-add input').catcomplete({
	delay: 0,
	source: function(request, response) {
			$.ajax({
				url: "<?= HTTP_ADMIN . "index.php?route=catalog/option/autocomplete"; ?>" + '&filter_name=' +	encodeURIComponent(request.term),
				dataType: 'json',
				success: function(json) {
						response($.map(json, function(item) {
							return {
									category: item.category,
									label: item.name,
									value: item.option_id,
									type: item.type,
									option_value: item.option_value
							}
						}));
				}
			});
	},
	select: function(event, ui) {
			if($('#tab-option-'+ui.item.value).length){
				$('.dup_option_warning').remove();
				$('#tab-option-'+ui.item.value).prepend("<div class='dup_option_warning warning'>This Option has already been added here!</span>");
				$('#option-' + ui.item.value).trigger('click');
				return;
			}
			html	= '<div id="tab-option-%option_id%" class="vtabs-content">';
			html += '	<input type="hidden" name="product_options[%option_id%][product_option_id]" value="" />';
			html += '	<input type="hidden" name="product_options[%option_id%][option_id]" value="%option_id%" />';
			html += '	<input type="hidden" name="product_options[%option_id%][name]" value="' + ui.item.label + '" />';
			html += '	<input type="hidden" name="product_options[%option_id%][type]" value="' + ui.item.type + '" />';
			html += '	<table class="form">';
			html += '		<tr>';
			html += '				<td><?= $entry_required; ?></td>';
			html += '				<td>' + "<?= $this->builder->build('select', $yes_no, "product_options[%option_id%][required]", 1); ?>" + '</td>';
			html += '		</tr>';
			html += '		<tr>';
			html += '				<td><?= $entry_sort_order; ?></td>';
			html += '				<td><input type="text" name="product_options[%option_id%][sort_order]" value="" /></td>';
			html += '		</tr>';
			html += '		<tr>';
			html += '				<td><?= $entry_option_value_list; ?></td>';
			html += '				<td>';
			html += '						<div class="scrollbox option_value_list clickable">';
			for(i=0; i< ui.item.option_value.length; i++){
				html += '							<div onclick="addOptionValue(%option_id%, ' + ui.item.option_value[i]['option_value_id'] + ',\'' + addslashes(ui.item.option_value[i]['name']) + '\'); $(this).remove();"><span class="po_label">' + ui.item.option_value[i]['name'] + '</span><img src="<?= HTTP_THEME_IMAGE . 'add.png'; ?>" /></div>';
			}
			html += '						</div>';
			html += '				</td>';
			html += '			</tr>';
			html += '	</table>';
				
			html += '	<table id="option-value%option_id%" class="list">';
			html += '		<thead>';
			html += '			<tr>';
			html += '				<td class="left"><?= $entry_option_value; ?></td>';
			html += '				<td class="right"><?= $entry_quantity; ?></td>';
			html += '				<td class="left"><?= $entry_subtract; ?></td>';
			html += '				<td class="right"><?= $entry_cost; ?></td>';
			html += '				<td class="right"><?= $entry_price; ?></td>';
			html += '				<td class="right"><?= $entry_option_points; ?></td>';
			html += '				<td class="right"><?= $entry_weight; ?></td>';
			html += '				<td class="right"><?= $entry_option_value_restriction; ?></td>';
			html += '				<td></td>';
			html += '			</tr>';
			html += '		</thead>';
			html += '		<tbody class="option_value_entries"></tbody>';
			html += '	</table>';
			html += '</div>';
	
			
			$('#tab-option').append(html.replace(/%option_id%/g, ui.item.value));
			
			$('#option-add').before('<a href="#tab-option-' + ui.item.value + '" id="option-' + ui.item.value + '">' + ui.item.label + '&nbsp;<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" alt="" onclick="$(\'#vtab-option a:first\').trigger(\'click\'); $(\'#option-' + ui.item.value + '\').remove(); $(\'#tab-option-' + ui.item.value + '\').remove(); update_ov_entries_select();return false;" /></a>');
			
			$('#vtab-option a').tabs();
			
			$('#option-' + ui.item.value).trigger('click');
			
			return false;
	}
});
//--></script>

<span id='all_product_option_values' style='display:none'>
	<?= $this->builder->set_config('option_value_id', 'name'); ?>
	<?= $this->builder->build('select', $all_product_option_values, "product_options[%option_id%][product_option_value][%option_value_id%][restrictions][%row%][restrict_option_value_id]", '', array('class'=>'restrict_option_values')); ?>
</span>
					
<script type="text/javascript">//<!--

function addOptionValue(option_id, option_value_id, name) {
	html = '	<tr>';
	html += '		<td class="left">';
	html += '			<span class="option_value_label">' + name + '</span>';
	html += '			<input class="ov_entry_name" type="hidden" name="product_options[%option_id%][product_option_value][%option_value_id%][name]" value="' + addslashes(name) + '" />';
	html += '			<input type="hidden" name="product_options[%option_id%][product_option_value][%option_value_id%][option_id]" value="%option_id%" />';
	html += '			<input class="ov_entry_option_value_id" type="hidden" name="product_options[%option_id%][product_option_value][%option_value_id%][option_value_id]" value="%option_value_id%" />';
	html += '		</td>';
	html += '		<td class="right"><input type="text" name="product_options[%option_id%][product_option_value][%option_value_id%][quantity]" value="1" size="3" /></td>';
	html += '		<td class="left">' + "<?= $this->builder->build('select', $yes_no, "product_options[%option_id%][product_option_value][%option_value_id%][subtract]",1); ?>" + '</td>';
	html += '		<td class="right"><input type="text" name="product_options[%option_id%][product_option_value][%option_value_id%][cost]" value="0" size="5" /></td>';
	html += '		<td class="right"><input type="text" name="product_options[%option_id%][product_option_value][%option_value_id%][price]" value="0" size="5" /></td>';
	html += '		<td class="right"><input type="text" name="product_options[%option_id%][product_option_value][%option_value_id%][points]" value="0" size="5" /></td>';
	html += '		<td class="right"><input type="text" name="product_options[%option_id%][product_option_value][%option_value_id%][weight]" value="0" size="5" /></td>';
	html += '		<td class="center">';
	html += '			<table class="list">';
	html += '				<thead>';
	html += '						<tr>';
	html += '							<td class="center"><?= $entry_restriction_option_value; ?></td>';
	html += '							<td class="center"><?= $entry_restriction_quantity; ?></td>';
	html += '							<td></td>';
	html += '						</tr>';
	html += '				</thead>';
	html += '				<tbody class="restrict_entries"></tbody>';
	html += '				<tfoot>';
	html += '						<tr>';
	html += '							<td colspan="2"></td>';
	html += '							<td class="center"><a onclick="add_restriction_value($(this),%option_id%,%option_value_id%)" class="button_add"></a></td>';
	html += '						</tr>';
	html += '				</tfoot>';
	html += '			</table>';
	html += '		</td>';
	html += '		<td class="left"><a onclick="remove_option_value($(this),%option_id%, %option_value_id%, \'' + addslashes(name) + '\')" class="button"><?= $button_remove; ?></a></td>';
	html += '	</tr>';
	
	$('#tab-option-' + option_id + ' .option_value_entries').append(html.replace(/%option_id%/g,option_id).replace(/%option_value_id%/g,option_value_id));
	
	update_ov_entries_select();
}

function remove_option_value(context, option_id, option_value_id, name){
	context.closest('tr').remove();
	$('#tab-option-'+option_id + ' .option_value_list').append('<div onclick="addOptionValue(' + option_id + ',' + option_value_id + ',\'' + addslashes(name) + '\'); $(this).remove();"><span class="po_label" >' + name + '</span><img src="<?= HTTP_THEME_IMAGE . 'add.png'; ?>" /></div>');
	
	update_ov_entries_select();
}

function update_ov_entries_select(){
	new_options = '';
	$('.option_value_entries > tr').each(function(i,e){
			name = $(e).find('.ov_entry_name').val();
			id = $(e).find('.ov_entry_option_value_id').val();
			new_options += '<option value="' + id + '">' + name + '</option>';
	});
	
	$('#all_product_option_values select, .restrict_entries select.restrict_option_values').each(function(i,e){
			select = $(e).val();
			$(e).html(new_options);
			$(e).val(select);
	});
	
}

var restrict_row = 0;

function add_restriction_value(context, option_id, option_value_id){
	html =	'<tr>';
				<?= $this->builder->set_config('product_option_value_id', 'name'); ?>
	html += '	<td class="center">' + $('#all_product_option_values').html() + '</td>';
	html += '	<td class="center"><input type="text" size="3" name="product_options[%option_id%][product_option_value][%option_value_id%][restrictions][%row%][quantity]" value="1" /></td>';
	html += '	<td class="center"><a onclick="$(this).closest(\'tr\').remove()" class="button_remove"></a></td>';
	html += '</tr>';
	
	context.closest('table').find('.restrict_entries').append(html.replace(/%option_id%/g,option_id).replace(/%option_value_id%/g, option_value_id).replace(/%row%/g, 'new'+restrict_row));
	restrict_row++;
}

function addslashes(text){
	return text.replace(/\'/g,'\\\'').replace(/\"/g,'\\"');
}

//--></script>

<script type="text/javascript">//<!--
var image_row = <?= $image_row; ?>;
function addImage(imageName) {
	html = '	<tr class="imagerow" id="image-row' + image_row + '">';
	html += '		<td class="left"><div class="image"><img width="100" src="../image/' + imageName + '" alt="' + imageName + '" title="' + imageName + '" id="thumb' + image_row + '" /><input type="hidden" name="product_images[' + image_row + '][image]" value="' + imageName + '" id="image' + image_row + '" /></div></td>';
	html += '		<td class="left">' + imageName + '</td>';
	html += '		<td class="right"><input type="radio" name="primary_product_image" value="' + imageName + '" /></td>';
	html += '		<td class="right"><input class="sortOrder" type="text" name="product_images[' + image_row + '][sort_order]" value="' + (image_row + 1) + '" size="2" /></td>';
	html += '		<td class="left"><a onclick="$(\'#image-row' + image_row	+ '\').remove();" class="button"><?= $button_remove; ?></a></td>';
	html += '	</tr>';
	
	$('#images tbody').append(html);
	
	image_row++;
		
		$('#images').sortable('refresh');
};

function addSingleImage(imageName, field, thumb, rows) {
	$.ajax({
			url: "<?= HTTP_ADMIN . "index.php?route=common/filemanager/image"; ?>" + '&image=' + encodeURIComponent(imageName),
			dataType: 'text',
			success: function(text) {
				$('#' + thumb).replaceWith('<img src="' + text + '" alt="" id="' + thumb + '" />');
				if (!rows || rows == -1) {
						$('#' + field).replaceWith('<input type="hidden" id="' + field +'" value="' + imageName + '" name="' + field + '">');
				} else {
						$('#' + field).replaceWith('<input type="hidden" id="' + field +'" value="' + imageName + '" name="product_images[' + rows + '][image]">');
				}
		}
	});
};

$('input[name=primary_product_image]').live("change", function() { $('input[name=image]').val($(this).val()); });

$('#images').bind('sortupdate', function(event, ui) {
	var index = 0;
	$('#images tbody tr').each(function() {
			index += 1;
			var so = $(this).find('.sortOrder');
			so.val(index);
	});
});
	
$(document).ready(function() {
	var c = {};
	$('#images tbody').sortable({	items: 'tr.imagerow',
																	forcePlaceholderSize:true,
																	cursor: "move",
																	helper: function(event) { return $('<div class="drag-row"><table></table></div>').find('table').append($(event.target).closest('tr').clone()).end(); },
																	forceHelperSize: true,
																	forcePlaceholderSize: true,
																	scroll: true,
																	scrollSensitivity: 30,
																	scrollSpeed: 30});
});
//--></script>

<?= $this->builder->js('datepicker'); ?>
<script type="text/javascript">//<!--
$('#tabs a').tabs();
//$('#languages a').tabs();
$('#vtab-option a').not('.normal').tabs();
//--></script>
<?= $this->builder->js('errors', $errors); ?>
<?= $footer; ?>