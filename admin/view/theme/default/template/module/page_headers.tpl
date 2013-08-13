<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
	<div class="heading">
		<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt="" /> <?= $head_title; ?></h1>
		<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
	</div>
	<div class="content">
		<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
			<table id="module" class="list">
				<thead>
					<tr>
						<td class="center"><?= $entry_layout; ?></td>
						<td class="center"><?= $entry_page_header; ?></td>
						<td class="center"><?= $entry_priority; ?></td>
						<td class="center"><?= $entry_status; ?></td>
						<td></td>
					</tr>
				</thead>
				<? $max_header_id = 0;?>
				<? foreach ($headers as $module_row=>$header) { ?>
				<? $max_header_id = max($module_row,$max_header_id);?>
				<tbody id="module-row<?= $module_row; ?>">
					<tr>
					<td class='center'>
						<? if(isset($header['layouts']))foreach($header['layouts'] as $layout_id){?>
						<div class="layout_header">
							<?= $this->builder->build('select',$layouts, "page_headers[$module_row][layouts][]", (int)$layout_id); ?>
							<a onclick="$(this).parent().remove();"><?= $button_remove; ?></a>
						</div>
						<? }?>
						<a onclick="add_layout_header(this)" name='layout_<?= $module_row; ?>'><?= $button_add_layout; ?></a>
					</td>
				<td class="center">
					<? if(count($languages)>1){?>
									<div id='languages-<?= $module_row; ?>' class="htabs">
										<? foreach ($languages as $language) { ?>
										<a href="#language-<?= $module_row; ?>-<?= $language['language_id']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /> <?= $language['name']; ?></a>
										<? } ?>
									</div>
							<? }?>
							<? foreach($header['page_header'] as $language_id=>$html){?>
						<div id='language-<?= $module_row.'-'.$language_id; ?>'><textarea id='page_header-<?= $module_row; ?>-<?= $language_id; ?>' class='ckedit' name="page_headers[<?= $module_row; ?>][page_header][<?= $language_id; ?>]"><?= $html; ?></textarea></div>
						<? }?>
				</td>
				<td class="center"><input type='text' name='page_headers[<?= $module_row; ?>][priority]' value='<?= $header['priority']; ?>' /></td>
				<td class='center'><?= $this->builder->build('select',$statuses,"page_headers[$module_row][status]",$header['status']); ?></td>
						<td class="center"><a onclick="$('#module-row<?= $module_row; ?>').remove();" class="button"><?= $button_remove; ?></a></td>
					</tr>
				</tbody>
				<? } ?>
				<tfoot>
					<tr>
						<td colspan="8"></td>
						<td class="left"><a onclick="addModule();" class="button"><?= $button_add_header; ?></a></td>
					</tr>
				</tfoot>
			</table>
		</form>
	</div>
</div>

<script type="text/javascript">//<!--
function add_layout_header(c){
	html	= '<div class="layout_header">';
	html += "	<?= $this->builder->build('select',$layouts, "page_headers[%modrow%][layouts][]"); ?>";
	html += '	<a onclick="$(this).parent().remove();"><?= $button_remove; ?></a>';
	html += '</div>';
	
	row = parseInt($(c).closest('tbody').attr('id').replace(/module-row/,''));
	$(c).before(html.replace('%modrow%',row));
}
//--></script>

<script type="text/javascript">//<!--
var module_row = <?= $max_header_id+1; ?>;
function addModule() {
	html	= '<tbody id="module-row%modrow%">';
	html += '	<tr>';
	html += '		<td class="center"><div class="layout_header"></div><a onclick="add_layout_header(this)"><?= $button_add_layout; ?></a></td>';
	html += '		<td class="center">';
	<? if(count($languages)>1){?>
	html += '<div id="languages-%modrow%" class="htabs">';
	<? foreach ($languages as $language) { ?>
	html += '	<a href="#language-%modrow%-<?= $language['language_id']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /> <?= $language['name']; ?></a>';
	<? } ?>
	html += '</div>';
	<? }?>
	<? foreach($languages as $lang){?>
	html += '			<div id="language-%modrow%-<?= $lang['language_id']; ?>"><textarea id="page_header_%modrow%-<?= $lang['language_id']; ?>" name="page_headers[%modrow%][page_header][<?= $lang['language_id']; ?>]" class="ckedit"></textarea></div>';
	<? }?>
	html += '		</td>';
	html += '		<td class="center"><input type="text" name="page_headers[%modrow%][priority]" value="0" /></td>';
	html += '		<td class="center">'+"<?= $this->builder->build('select',$statuses,"page_headers[%modrow%][status]",1); ?>"+'</td>';
	html += '		<td class="center"><a onclick="$(\'#module-row%modrow%\').remove();" class="button"><?= $button_remove; ?></a></td>';
	html += '	</tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html.replace(/%modrow%/g,module_row));
	<? foreach ($languages as $language) { ?>
	init_ckeditor_for($('#page_header_'+module_row + '-<?= $language['language_id']; ?>'));
	<? }?>
	$('#languages-'+module_row + ' a').tabs();
	module_row++;
}
//--></script>
<script type="text/javascript">//<!--
<? foreach ($headers as $hid=>$h) { ?>
$('#languages-<?= $hid; ?> a').tabs();
<? }?>
//--></script>
<?= $this->builder->js('ckeditor'); ?>
<?= $this->builder->js('errors'); ?>
<?= $footer; ?>