<?= $header; ?>
<div class="content">
<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt=""/> <?= $head_title; ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a
					href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td><?= $entry_product_filter; ?></td>
						<td><input type="text" id='add_product_filter' name="filter_type" value=""/></td>
					</tr>
					<tr>
						<td><?= $entry_product_filter_help; ?></td>
						<td>
							<ul id="product_filter" class="scrollbox">
								<? foreach ($product_filter_types as $filter_id => $filter) { ?>
									<li class="product_filter_item" filter_id="<?= $filter_id; ?>">
										<div class='filter_name'><?= $filter; ?></div>
										<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>"/>

										<div
											class='filter_default <?= $default_product_filter == $filter_id ? 'is_default' : ''; ?>'><?= $default_product_filter == $filter_id ? "default" : "<a onclick=\"make_default_filter('$filter_id');\">make default</a>"; ?></div>
										<input type="hidden" name="product_filter_types[<?= $filter_id; ?>]"
										       value="<?= $filter ?>"/>
									</li>
								<? } ?>
							</ul>
							<input type="hidden" id='default_product_filter' name="default_product_filter"
							       value="<?= $default_product_filter; ?>"/>
					</tr>
				</table>
				<table class="form">
					<tr>
						<td><?= $entry_product; ?></td>
						<td><input type="text" name="product" value=""/></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							<div id="featured-product" class="scrollbox">
								<? foreach ($featured_product as $product) { ?>
									<div id="featured-product<?= $product['product_id']; ?>"><?= $product['name']; ?> <img
											src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>"/>
										<input type="hidden" name='featured_product[]' value="<?= $product['product_id']; ?>"/>
									</div>
								<? } ?>
							</div>
					</tr>
				</table>
				<table id="module" class="list">
					<thead>
					<tr>
						<td class="left"><?= $entry_limit; ?></td>
						<td class="left"><?= $entry_image; ?></td>
						<td class="left"><?= $entry_layout; ?></td>
						<td class="left"><?= $entry_position; ?></td>
						<td class="left"><?= $entry_filter_menu_position; ?></td>
						<td class="left"><?= $entry_display_style; ?></td>
						<td class="left"><?= $entry_status; ?></td>
						<td class="right"><?= $entry_sort_order; ?></td>
						<td></td>
					</tr>
					</thead>
					<? $module_row = 0; ?>
					<? foreach ($modules as $module) { ?>
						<? if (isset($module['fm_id'])) {
							continue;
						} ?>
						<tbody id="module-row<?= $module_row; ?>">
						<tr>
							<td class="left"><input type="text" name="featured_module[<?= $module_row; ?>][limit]"
							                        value="<?= $module['limit']; ?>" size="1"/></td>
							<td class="left"><input type="text" name="featured_module[<?= $module_row; ?>][image_width]"
							                        value="<?= $module['image_width']; ?>" size="3"/>
								<input type="text" name="featured_module[<?= $module_row; ?>][image_height]"
								       value="<?= $module['image_height']; ?>" size="3"/>
							</td>
							<td class="left"><select name="featured_module[<?= $module_row; ?>][layout_id]">
									<? foreach ($layouts as $layout) { ?>
										<? if ($layout['layout_id'] == $module['layout_id']) { ?>
											<option value="<?= $layout['layout_id']; ?>"
											        selected="selected"><?= $layout['name']; ?></option>
										<? } else { ?>
											<option value="<?= $layout['layout_id']; ?>"><?= $layout['name']; ?></option>
										<? } ?>
									<? } ?>
								</select></td>
							<td class="left"><select name="featured_module[<?= $module_row; ?>][position]">
									<? if ($module['position'] == 'content_top') { ?>
										<option value="content_top" selected="selected"><?= $text_content_top; ?></option>
									<? } else { ?>
										<option value="content_top"><?= $text_content_top; ?></option>
									<? } ?>
									<? if ($module['position'] == 'content_bottom') { ?>
										<option value="content_bottom" selected="selected"><?= $text_content_bottom; ?></option>
									<? } else { ?>
										<option value="content_bottom"><?= $text_content_bottom; ?></option>
									<? } ?>
									<? if ($module['position'] == 'column_left') { ?>
										<option value="column_left" selected="selected"><?= $text_column_left; ?></option>
									<? } else { ?>
										<option value="column_left"><?= $text_column_left; ?></option>
									<? } ?>
									<? if ($module['position'] == 'column_right') { ?>
										<option value="column_right" selected="selected"><?= $text_column_right; ?></option>
									<? } else { ?>
										<option value="column_right"><?= $text_column_right; ?></option>
									<? } ?>
								</select></td>
							<td class="left"><select name="featured_module[<?= $module_row; ?>][filter_menu_position]">
									<? if ($module['filter_menu_position'] == 'content_top') { ?>
										<option value="content_top" selected="selected"><?= $text_content_top; ?></option>
									<? } else { ?>
										<option value="content_top"><?= $text_content_top; ?></option>
									<? } ?>
									<? if ($module['filter_menu_position'] == 'content_bottom') { ?>
										<option value="content_bottom" selected="selected"><?= $text_content_bottom; ?></option>
									<? } else { ?>
										<option value="content_bottom"><?= $text_content_bottom; ?></option>
									<? } ?>
									<? if ($module['filter_menu_position'] == 'column_left') { ?>
										<option value="column_left" selected="selected"><?= $text_column_left; ?></option>
									<? } else { ?>
										<option value="column_left"><?= $text_column_left; ?></option>
									<? } ?>
									<? if ($module['filter_menu_position'] == 'column_right') { ?>
										<option value="column_right" selected="selected"><?= $text_column_right; ?></option>
									<? } else { ?>
										<option value="column_right"><?= $text_column_right; ?></option>
									<? } ?>
								</select></td>
							<td class="left">
								<?= $this->builder->build('select', $display_styles, "featured_module[$module_row][display]", $module['display']); ?>
							</td>
							<td class="left"><select name="featured_module[<?= $module_row; ?>][status]">
									<? if ($module['status']) { ?>
										<option value="1" selected="selected"><?= $text_enabled; ?></option>
										<option value="0"><?= $text_disabled; ?></option>
									<? } else { ?>
										<option value="1"><?= $text_enabled; ?></option>
										<option value="0" selected="selected"><?= $text_disabled; ?></option>
									<? } ?>
								</select></td>
							<td class="right"><input type="text" name="featured_module[<?= $module_row; ?>][sort_order]"
							                         value="<?= $module['sort_order']; ?>" size="3"/></td>
							<td class="left"><a onclick="$('#module-row<?= $module_row; ?>').remove();"
							                    class="button"><?= $button_remove; ?></a></td>
						</tr>
						</tbody>
						<? $module_row++; ?>
					<? } ?>
					<tfoot>
					<tr>
						<td colspan="6"></td>
						<td class="left"><a onclick="addModule();" class="button"><?= $button_add_module; ?></a></td>
					</tr>
					</tfoot>
				</table>
			</form>
		</div>
	</div>

	<script type="text/javascript"><!--
		$(document).ready(function () {
			$('#product_filter, #featured-product').sortable({revert: true});
		});
		--></script>
	<script type="text/javascript"><!--
		$('input[name=\'product\']').autocomplete({
			delay: 0,
			source: function (request, response) {
				$.ajax({
					url: "<?= HTTP_ADMIN . "index.php?route=catalog/product/autocomplete"; ?>" + '&filter_name=' + encodeURIComponent(request.term),
					dataType: 'json',
					success: function (json) {
						response($.map(json, function (item) {
							return {
								label: item.name,
								value: item.product_id
							}
						}));
					}
				});

			},
			select: function (event, ui) {
				$('#featured-product' + ui.item.value).remove();

				$('#featured-product').append('<div id="featured-product' + ui.item.value + '">' + ui.item.label + '<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" /><input type="hidden" name="featured_product[]" value="' + ui.item.value + '" /></div>');

				data = $.map($('#featured-product input'), function (element) {
					return $(element).attr('value');
				});

				$('input[name=\'featured_product\']').attr('value', data.join());

				return false;
			}
		});

		$('#featured-product div img').live('click', function () {
			$(this).parent().remove();

			$('#featured-product div:odd').attr('class', 'odd');
			$('#featured-product div:even').attr('class', 'even');

			data = $.map($('#featured-product input'), function (element) {
				return $(element).attr('value');
			});

			$('input[name=\'featured_product\']').attr('value', data.join());
		});
		//--></script>
	<script type="text/javascript"><!--
		var module_row = <?= $module_row; ?>;

		function addModule() {
			html = '<tbody id="module-row' + module_row + '">';
			html += '	<tr>';
			html += '		<<td class="left"><input type="text" name="featured_module[' + module_row + '][limit]" value="12" size="1" /></td>';
			html += '		<td class="left"><input type="text" name="featured_module[' + module_row + '][image_width]" value="174" size="3" /> <input type="text" name="featured_module[' + module_row + '][image_height]" value="135" size="3" /></td>';
			html += '		<td class="left"><select name="featured_module[' + module_row + '][layout_id]">';
			<? foreach ($layouts as $layout) { ?>
			html += '			<option value="<?= $layout['layout_id']; ?>"><?= addslashes($layout['name']); ?></option>';
			<? } ?>
			html += '		</select></td>';
			html += '		<td class="left"><select name="featured_module[' + module_row + '][position]">';
			html += '			<option value="content_top"><?= $text_content_top; ?></option>';
			html += '			<option value="content_bottom"><?= $text_content_bottom; ?></option>';
			html += '			<option value="column_left"><?= $text_column_left; ?></option>';
			html += '			<option value="column_right"><?= $text_column_right; ?></option>';
			html += '		</select></td>';
			html += '		<td class="left"><select name="featured_module[' + module_row + '][filter_menu_position]">';
			html += '			<option value="content_top"><?= $text_content_top; ?></option>';
			html += '			<option value="content_bottom"><?= $text_content_bottom; ?></option>';
			html += '			<option value="column_left" selected="selected"><?= $text_column_left; ?></option>';
			html += '			<option value="column_right"><?= $text_column_right; ?></option>';
			html += '		</select></td>';
			html += '		<td class="left">';
			html += "<?= $this->builder->build('select',$display_styles, "featured_module[%modrow%][display]", 'popup'); ?>".replace(/%modrow%/, module_row);
			html += '		</td>';
			html += '		<td class="left"><select name="featured_module[' + module_row + '][status]">';
			html += '			<option value="1" selected="selected"><?= $text_enabled; ?></option>';
			html += '			<option value="0"><?= $text_disabled; ?></option>';
			html += '		</select></td>';
			html += '		<td class="right"><input type="text" name="featured_module[' + module_row + '][sort_order]" value="2" size="3" /></td>';
			html += '		<td class="left"><a onclick="$(\'#module-row' + module_row + '\').remove();" class="button"><?= $button_remove; ?></a></td>';
			html += '	</tr>';
			html += '</tbody>';

			$('#module tfoot').before(html);

			module_row++;
		}
		//--></script>
	<script type="text/javascript"><!--
		$('#add_product_filter').keyup(add_product_filter_to_list);
		$('ul#product_filter li img').live('click', function () {
			if ($(this).closest('.product_filter_item').find('.filter_default').hasClass('is_default'))
				make_default_filter($('#product_filter .product_filter_item:first').attr('filter_id'));
			$(this).parent().remove();
		});


		function add_product_filter_to_list(event) {
			if (event.which != 13)return;
			filter = $(this).attr('value');
			filter_id = filter.replace(/[\s-]/g, '_').replace(/['"`#!$]/, '').toLowerCase();
			$('.product_filter_item').each(function (i, e) {
				if ($(e).attr('filter_id') == filter_id) {
					filter_id = "";
					return false;
				}
			});
			if (filter_id == "")return;

			html = '<li class="product_filter_item" filter_id="' + filter_id + '">';
			html += '	<div class="filter_name">' + filter + '</div>';
			html += '	<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />';
			html += '	<div class="filter_default"><a onclick="make_default_filter(\'' + filter_id + '\');">make default</a></div>';
			html += '	<input type="hidden" name="product_filter_types[' + filter_id + ']" value="' + filter + '" />';
			html += '</li>';
			$('#product_filter').append(html);
			$(this).attr('value', '');
		}

		function make_default_filter(filter_id) {
			$('#default_product_filter').attr('value', filter_id);
			default_filter = $('.product_filter_item[filter_id="' + filter_id + '"] .filter_default');
			if (default_filter.hasClass('is_default'))return;
			old_filter_id = $('.is_default').closest('.product_filter_item').attr('filter_id');
			$('.is_default').html('<a onclick="make_default_filter(\'' + old_filter_id + '\');">make default</a>').removeClass('is_default');
			$('.product_filter_item[filter_id="' + filter_id + '"] .filter_default').html('default').addClass('is_default');

		}
		//--></script>

<?= $this->builder->js('errors'); ?>
<?= $footer; ?>