<?= $header; ?>
<style>
table#janrain_table tr td{vertical-align:top;}
table#janrain_table a{text-decoration:none;color:#1111CC}
table#janrain_table a:hover{text-decoration:underline;}
table#janrain_table .janrain_img{ margin:0 0 3px 10px;vertical-align:middle;}
.janrain_label_desc{font-family:Tahoma;font-size:11px;padding-top:5px;display:block;font-weight:normal;}

.janrain-icon-small{
	background: url(<?= $image_url; ?>) no-repeat top left;
	width:16px;
	height:16px;
}

.display_icon_list{
	max-width: 600px;
}
.display_icon_label{
	float:left;
	margin-bottom:10px;
}
.display_icon_label div, .display_icon_label label, .display_icon_label input{
	float:left;
}
</style>

<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<?= $this->builder->display_errors($errors); ?>
<div class="box">
	<div class="heading">
		<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt="" /><?= $heading_title; ?></h1>
		<div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?= $button_save; ?></span></a><a onclick="location = '<?= $cancel; ?>';" class="button"><span><?= $button_cancel; ?></span></a></div>
	</div>
	<div class="content">
		<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
			<table class="form" id="janrain_table">
				<tr>
					<td>*<?= $entry_application_domain; ?></td>
					<td valign="top"><input style="width:350px;" type="text" name="janrain_application_domain" value="<?= $janrain_application_domain; ?>" /><br /><span class="janrain_label_desc"><?= $entry_application_domain_desc; ?></span></td>
				</tr>
		<tr>
					<td>*<?= $entry_api_key; ?></td>
					<td valign="top"><input style="width:350px;" type="text" name="janrain_api_key" value="<?= $janrain_api_key; ?>" /><br /><span class="janrain_label_desc"><?= $entry_api_key_desc; ?></span></td>
				</tr>
		<tr>
				<td><?= $entry_display_icons; ?></td>
				<td valign="top">
						<div	class='display_icon_list'>
						<? foreach($display_icons as $key=>$icon){ ?>
							<div class='display_icon_label'>
									<input id='icon-<?= $key; ?>' type='checkbox' name="janrain_display_icons[]" value="<?= $key; ?>" <?= $janrain_display_icons?(in_array($key,$janrain_display_icons)?'checked="checked"':''):'';?> />
									<label for='icon-<?= $key; ?>'><div class='janrain-icon-small' style='background-position:0 <?= $image_offset[$key]*-16; ?>px'></div>&nbsp;<?= $icon; ?></label>
							</div>
						<? }?>
						</div>
				</td>
			</tr>
		<tr>
					<td><?= $entry_login_redir; ?></td>
					<td valign="top"><input style="width:350px;" type="text" name="janrain_login_redir" value="<?= $janrain_login_redir; ?>" /><br /><span class="janrain_label_desc"><?= $entry_login_redir_desc; ?></span></td>
				</tr>
		<tr>
					<td><?= $entry_logout_redir; ?></td>
					<td valign="top"><input style="width:350px;" type="text" name="janrain_logout_redir" value="<?= $janrain_logout_redir; ?>" /><br /><span class="janrain_label_desc"><?= $entry_logout_redir_desc; ?></span></td>
				</tr>
	</table>
	<table id="module" class="list">
		<thead>
					<tr>
						<td class="center"><?= $entry_layout; ?></td>
				<td class="center"><?= $entry_display_type; ?></td>
				<td class="center"><?= $entry_icon_size; ?></td>
						<td class="center"><?= $entry_position; ?></td>
						<td class="center"><?= $entry_status; ?></td>
						<td class="center"><?= $entry_sort_order; ?></td>
						<td></td>
					</tr>
				</thead>
			<? $module_row = 0; ?>
				<? foreach ($modules as $module) { ?>
				<tbody id="module-row<?= $module_row; ?>">
					<tr>
						<td class="center">
							<? $this->builder->set_config('layout_id','name');?>
							<?= $this->builder->build('select',$layouts, "janrain_module[$module_row][layout_id]", $module['layout_id']); ?>
						</td>
				<td class="center">
					<?= $this->builder->build('select',$display_types,"janrain_module[$module_row][display_type]", $module['display_type']); ?>
				</td>
				<td class="center">
							<?= $this->builder->build('select',$icon_sizes,"janrain_module[$module_row][icon_size]", $module['icon_size']); ?>
						</td>
				<td class="center">
							<?= $this->builder->build('select',$positions,"janrain_module[$module_row][position]", $module['position']); ?>
						</td>
						<td class="center">
							<?= $this->builder->build('select',$statuses,"janrain_module[$module_row][status]", (int)$module['status']); ?>
						</td>
						<td class="center"><input type="text" name="janrain_module[<?= $module_row; ?>][sort_order]" value="<?= $module['sort_order']; ?>" size="3" /></td>
						<td class="left"><a onclick="$('#module-row<?= $module_row; ?>').remove();" class="button"><span><?= $button_remove; ?></span></a></td>
					</tr>
				</tbody>
				<? $module_row++; ?>
				<? } ?>
		<tfoot>
					<tr>
						<td colspan="7"></td>
						<td class="left"><a onclick="addModule();" class="button"><span><?= $button_add_module; ?></span></a></td>
					</tr>
				</tfoot>
			</table>
		</form>
	</div>
</div>
<script type="text/javascript"><!--
var module_row = <?= $module_row; ?>;

function addModule() {
	html	= '<tbody id="module-row%modrow%">';
	html += '	<tr>';
		<? $this->builder->set_config('layout_id','name');?>
	html += '		<td class="center">' + "<?= $this->builder->build('select',$layouts, "janrain_module[%modrow%][layout_id]"); ?>" + '</td>';
	html += '		<td class="center">' + "<?= $this->builder->build('select',$display_types,"janrain_module[%modrow%][display_type]"); ?>" + '</td>';
	html += '		<td class="center">' + "<?= $this->builder->build('select',$icon_sizes,"janrain_module[%modrow%][icon_size]"); ?>" + '</td>';
	html += '		<td class="center">' + "<?= $this->builder->build('select',$positions,"janrain_module[%modrow%][position]"); ?>" + '</td>';
	html += '		<td class="center">' + "<?= $this->builder->build('select',$statuses,"janrain_module[%modrow%][status]"); ?>" + '</td>';
	html += '		<td class="center"><input type="text" name="janrain_module[%modrow%][sort_order]" value="" size="3" /></td>';
	html += '		<td class="left"><a onclick="$(\'#module-row%modrow%\').remove();" class="button"><span><?= $button_remove; ?></span></a></td>';
	html += '	</tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html.replace(/%modrow%/g,module_row));
	
	module_row++;
}

$('#form').bind('submit', function() {
	var module = new Array();

	$('#module tbody').each(function(index, element) {
		module[index] = $(element).attr('id').substr(10);
	});
	
	$('input[name=\'janrain_module\']').attr('value', module.join(','));
});
//--></script>