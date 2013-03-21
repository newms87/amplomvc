<?= $header; ?>
<div class="content">
<?= $this->builder->display_breadcrumbs();?>
<? if ($error_warning) { ?>
<div class="message_box warning"><?= $error_warning; ?></div>
<? } ?>
<div class="box">
  <div class="heading">
    <h1><img src="view/image/module.png" alt="" /> <?= $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
  </div>
  <div class="content">
    <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table id="module" class="list">
        <thead>
          <tr>
            <td class="left"><?= $entry_banner; ?></td>
            <td class="left"><span class="required">*</span> <?= $entry_dimension; ?></td>
            <td class="left"><?= $entry_layout; ?></td>
            <td class="left"><?= $entry_position; ?></td>
            <td class="left"><?= $entry_status; ?></td>
            <td class="right"><?= $entry_sort_order; ?></td>
            <td></td>
          </tr>
        </thead>
        <? $module_row = 0; ?>
        <? foreach ($modules as $module) { ?>
        <tbody id="module-row<?= $module_row; ?>">
          <tr>
            <td class="left"><select name="banner_module[<?= $module_row; ?>][banner_id]">
                <? foreach ($banners as $banner) { ?>
                <? if ($banner['banner_id'] == $module['banner_id']) { ?>
                <option value="<?= $banner['banner_id']; ?>" selected="selected"><?= $banner['name']; ?></option>
                <? } else { ?>
                <option value="<?= $banner['banner_id']; ?>"><?= $banner['name']; ?></option>
                <? } ?>
                <? } ?>
              </select></td>
            <td class="left"><input type="text" name="banner_module[<?= $module_row; ?>][width]" value="<?= $module['width']; ?>" size="3" />
              <input type="text" name="banner_module[<?= $module_row; ?>][height]" value="<?= $module['height']; ?>" size="3" />
              <? if (isset($error_dimension[$module_row])) { ?>
              <span class="error"><?= $error_dimension[$module_row]; ?></span>
              <? } ?></td>
            <td class="left"><select name="banner_module[<?= $module_row; ?>][layout_id]">
                <? foreach ($layouts as $layout) { ?>
                <? if ($layout['layout_id'] == $module['layout_id']) { ?>
                <option value="<?= $layout['layout_id']; ?>" selected="selected"><?= $layout['name']; ?></option>
                <? } else { ?>
                <option value="<?= $layout['layout_id']; ?>"><?= $layout['name']; ?></option>
                <? } ?>
                <? } ?>
              </select></td>
            <td class="left"><select name="banner_module[<?= $module_row; ?>][position]">
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
            <td class="left"><select name="banner_module[<?= $module_row; ?>][status]">
                <? if ($module['status']) { ?>
                <option value="1" selected="selected"><?= $text_enabled; ?></option>
                <option value="0"><?= $text_disabled; ?></option>
                <? } else { ?>
                <option value="1"><?= $text_enabled; ?></option>
                <option value="0" selected="selected"><?= $text_disabled; ?></option>
                <? } ?>
              </select></td>
            <td class="right"><input type="text" name="banner_module[<?= $module_row; ?>][sort_order]" value="<?= $module['sort_order']; ?>" size="3" /></td>
            <td class="left"><a onclick="$('#module-row<?= $module_row; ?>').remove();" class="button"><?= $button_remove; ?></a></td>
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
var module_row = <?= $module_row; ?>;

function addModule() {	
	html  = '<tbody id="module-row' + module_row + '">';
	html += '  <tr>';
	html += '    <td class="left"><select name="banner_module[' + module_row + '][banner_id]">';
	<? foreach ($banners as $banner) { ?>
	html += '      <option value="<?= $banner['banner_id']; ?>"><?= addslashes($banner['name']); ?></option>';
	<? } ?>
	html += '    </select></td>';
	html += '    <td class="left"><input type="text" name="banner_module[' + module_row + '][width]" value="" size="3" /> <input type="text" name="banner_module[' + module_row + '][height]" value="" size="3" /></td>';
	html += '    <td class="left"><select name="banner_module[' + module_row + '][layout_id]">';
	<? foreach ($layouts as $layout) { ?>
	html += '      <option value="<?= $layout['layout_id']; ?>"><?= addslashes($layout['name']); ?></option>';
	<? } ?>
	html += '    </select></td>';
	html += '    <td class="left"><select name="banner_module[' + module_row + '][position]">';
	html += '      <option value="content_top"><?= $text_content_top; ?></option>';
	html += '      <option value="content_bottom"><?= $text_content_bottom; ?></option>';
	html += '      <option value="column_left"><?= $text_column_left; ?></option>';
	html += '      <option value="column_right"><?= $text_column_right; ?></option>';
	html += '    </select></td>';
	html += '    <td class="left"><select name="banner_module[' + module_row + '][status]">';
    html += '      <option value="1" selected="selected"><?= $text_enabled; ?></option>';
    html += '      <option value="0"><?= $text_disabled; ?></option>';
    html += '    </select></td>';
	html += '    <td class="right"><input type="text" name="banner_module[' + module_row + '][sort_order]" value="" size="3" /></td>';
	html += '    <td class="left"><a onclick="$(\'#module-row' + module_row + '\').remove();" class="button"><?= $button_remove; ?></a></td>';
	html += '  </tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html);
	
	module_row++;
}
//--></script>
<?= $footer; ?>