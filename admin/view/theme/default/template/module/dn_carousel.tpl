<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div class="vtabs">
					<? $module_row = 1; ?>
					<? foreach ($modules as $module) { ?>
					<a href="#tab-module-<?= $module_row; ?>" id="module-<?= $module_row; ?>"><div id='tab-module-<?= $module_row?>-title' class="tab_title"><?= $module['title'][$lang_id]; ?></div>&nbsp;<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" alt="" onclick="$('.vtabs a:first').trigger('click'); $('#module-<?= $module_row; ?>').remove(); $('#tab-module-<?= $module_row; ?>').remove(); return false;" /></a>
					<? $module_row++; ?>
					<? } ?>
					<span id="module-add"><?= $button_add_module; ?>&nbsp;<img src="<?= HTTP_THEME_IMAGE . 'add.png'; ?>" alt="" onclick="addModule();" /></span> </div>
				<? $module_row = 1; ?>
				<? foreach ($modules as $module) { ?>
				<div id="tab-module-<?= $module_row; ?>" class="vtabs-content">
					<div id="language-<?= $module_row; ?>" class="htabs">
						<? foreach ($languages as $language) {if($language['status']){ ?>
						<a href="#tab-language-<?= $module_row; ?>-<?= $language['language_id']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /> <?= $language['name']; ?></a>
						<? }} ?>
					</div>
					
					<? foreach ($languages as $language) {if($language['status']){ ?>
					<div id="tab-language-<?= $module_row . '-' . $language['language_id']; ?>">
						<table class="form">
							<tr>
								<td><?= $entry_title; ?></td>
								<td><input class="car_title" max_length="20" size="20" name="dn_carousel_module[<?= $module_row; ?>][title][<?= $language['language_id']; ?>]" id="title-<?= $module_row; ?>-<?= $language['language_id']; ?>" value="<?= isset($module['title'][$language['language_id']]) ? $module['title'][$language['language_id']] : ''; ?>" /></td>
							</tr>
							<tr>
					<td class="left"><?= $entry_layout; ?></td>
					<td><?= $this->builder->build('select',$layouts, "dn_carousel_module[$module_row][layout_id]", $module['layout_id']); ?></td>
							</tr>
							<tr>
					<td class="left"><?= $entry_position; ?></td>
					<td><?= $this->builder->build('select',$positions, "dn_carousel_module[$module_row][position]", $module['position']); ?></td>
							</tr>
							<tr>
					<td class="left"><?= $entry_sort_order; ?></td>
					<td><input type='text' max_length='3' size='3' name="dn_carousel_module[<?= $module_row; ?>][sort_order]" value="<?= $module['sort_order']; ?>" /></td>
							</tr>
						</table>
						<table class="form">
							<tr>
							<td><?= $entry_add_article; ?></td>
							<td><input type="text" modrow="<?= $module_row; ?>" class="rel_article" name="rel_article" value="" /></td>
						</tr>
							<tr>
				<td><?= $entry_slides; ?></td>
								<td modrow='<?= $module_row?>'>
									<? $md_row = 1;?>
									<? if(isset($module['data']))foreach($module['data'] as $md){ ?>
										<div class='slide_data' slidenum="<?= $md_row; ?>">
												<!--<label><?= $entry_article_title; ?></label>-->
												<div class='slide_article_title'><?= $md['article_title']; ?></div><br />
											<input type="hidden" name="dn_carousel_module[<?= $module_row; ?>][data][<?= $md_row; ?>][article_id]" value="<?= $md['article_id']; ?>" />
											<?= isset($error_dn_carousel_module[$module_row]['data'][$md_row][$language['language_id']])?"<span class='error'>$error_dn_carousel_module[$module_row]['data'][$md_row][$language[language_id]]</span>":""; ?>
											<div class="image">
											<img src="<?= $md['thumb']; ?>" alt="" id="thumb<?= "$module_row-$md_row"; ?>" />
											<input type="hidden" name="dn_carousel_module[<?= $module_row; ?>][data][<?= $md_row; ?>][image]" value="<?= $md['image']; ?>" id="image<?= "$module_row-$md_row"; ?>"	/>
										<br />
											<a onclick="upload_image('image<?= "$module_row-$md_row"; ?>', 'thumb<?= "$module_row-$md_row"; ?>');"><?= $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
											<a onclick="$('#thumb<?= "$module_row-$md_row"; ?>').attr('src', '<?= $no_image; ?>'); $('#image<?= "$module_row-$md_row"; ?>').attr('value', '');"><?= $text_clear; ?></a>
										</div>
											<br/>
											<label><?= $entry_title_text; ?></label>
							<input type='text' name="dn_carousel_module[<?= $module_row; ?>][data][<?= $md_row; ?>][display_title]" value="<?= $md['display_title']; ?>" /><br />
											<label><?= $entry_description; ?></label>
											<textarea name="dn_carousel_module[<?= $module_row; ?>][data][<?= $md_row; ?>][description]"><?= isset($md['description']) ? $md['description']:''; ?></textarea>
												<img class='remove_slide_img' src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" alt="" onclick="remove_article_slide(<?= $module_row . ',' . $md_row; ?>);" />
										</div>
										<? $md_row++;?>
									<? } ?>
				</td>
							</tr>
						</table>
					</div>
					<? }} ?>
					
					<table class="form">
						<tr>
							<td><?= $entry_status; ?></td>
							<td><select name="dn_carousel_module[<?= $module_row; ?>][status]">
									<? if ($module['status']) { ?>
									<option value="1" selected="selected"><?= $text_enabled; ?></option>
									<option value="0"><?= $text_disabled; ?></option>
									<? } else { ?>
									<option value="1"><?= $text_enabled; ?></option>
									<option value="0" selected="selected"><?= $text_disabled; ?></option>
									<? } ?>
								</select></td>
						</tr>
					</table>
				</div>
				<? $module_row++; ?>
				<? } ?>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript"><!--
var module_row = <?= $module_row; ?>;

function addModule() {
	html	= '<div id="tab-module-' + module_row + '" class="vtabs-content">';
	html += '	<div id="language-' + module_row + '" class="htabs">';
		<? foreach ($languages as $language) {if($language['status']){ ?>
		html += '		<a href="#tab-language-'+ module_row + '-<?= $language['language_id']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /> <?= $language['name']; ?></a>';
		<? }} ?>
	html += '	</div>';
	
	<? foreach ($languages as $language) {if($language['status']){ ?>
	html += '		<div id="tab-language-'+ module_row + '-<?= $language['language_id']; ?>">';
	html += '			<table class="form">';
	html += '				<tr>';
	html += '					<td><?= $entry_title; ?></td>';
	html += '					<td><input class="car_title" value="<?= $tab_carousel; ?> ' + module_row + '" name="dn_carousel_module[' + module_row + '][title][<?= $language['language_id']; ?>]" id="title-' + module_row + '-<?= $language['language_id']; ?>" maxlength="20" size="20" /></td>';
	html += '				</tr>';
	html += '			<tr>';
	html += '			<td class="left"><?= $entry_layout; ?></td>';
	var layout_dd = "<?= $this->builder->build('select',$layouts, "dn_carousel_module[%module_row%][layout_id]"); ?>".replace(/%module_row%/,module_row);
	html += '			<td>'+layout_dd+'</td>';
	html += '			</tr>';
	html += '			<tr>';
	html += '			<td class="left"><?= $entry_position; ?></td>';
	var position_dd = "<?= $this->builder->build('select',$positions, "dn_carousel_module[%module_row%][position]"); ?>".replace(/%module_row%/,module_row);
	html += '			<td>'+position_dd+'</td>';
	html += '			</tr>';
	html += '			<tr>';
	html += '			<td class="left"><?= $entry_sort_order; ?></td>';
	html += '			<td><input type="text" max_length="3" size="3" name="dn_carousel_module['+module_row+'][sort_order]" value="0" /></td>';
	html += '			</tr>';
	html += '			</table>';
	html += '		</div>';
	<? }} ?>
	html += '<table class="form">';
	html += '<tr>';
		html += '	<td><?= $entry_add_article; ?></td>';
		html += '	<td><input type="text" class="rel_article" modrow="' + module_row + '" name="rel_article" value="" /></td>';
	html += '</tr>';
	
	html += '<tr>';
	html += '	<td><?= $entry_slides; ?></td>';
	html += '	<td modrow="' + module_row + '"></td>';
	html += '</tr>';

	html += '	<table class="form">';
	html += '		<tr>';
	html += '			<td><?= $entry_status; ?></td>';
	html += '			<td><select name="dn_carousel_module[' + module_row + '][status]">';
	html += '				<option value="1"><?= $text_enabled; ?></option>';
	html += '				<option value="0"><?= $text_disabled; ?></option>';
	html += '			</select></td>';
	html += '		</tr>';
	html += '	</table>';
	html += '</div>';
	
	$('#form').append(html);
	
	$('#language-' + module_row + ' a').tabs();
	
	$('#module-add').before('<a href="#tab-module-' + module_row + '" id="module-' + module_row + '"><div id="tab-module-'+module_row+'-title" class="tab_title"><?= $tab_carousel; ?> ' + module_row + '</div>&nbsp;<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" alt="" onclick="$(\'.vtabs a:first\').trigger(\'click\'); $(\'#module-' + module_row + '\').remove(); $(\'#tab-module-' + module_row + '\').remove(); return false;" /></a>');
	
	$('.vtabs a').tabs();
	
	$('#module-' + module_row).trigger('click');
	
	set_autocomplete($('#tab-module-' + module_row + ' .rel_article'));
	module_row++;
}

$('.car_title').live('keyup',function(){
	href = $(this).closest('.vtabs-content').attr('id');
	$("#" + href +"-title").html($(this).attr('value'));
});
//--></script>
<script type="text/javascript"><!--
$('.vtabs a').tabs();
//--></script>

<script type="text/javascript"><!--
set_autocomplete($('.rel_article'));
function set_autocomplete(selector){
	selector.autocomplete({
		delay: 0,
		source: function(request, response) {
			$.ajax({
				url: "<?= HTTP_ADMIN . "index.php?route=cms/article/autocomplete"; ?>" + '&filter_title=' +	encodeURIComponent(request.term),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item.title,
							value: item.article_id
						}
					}));
				}
			});
			
		},
		select: function(event, ui) {
			add_article_slide($(this).attr('modrow'), ui.item.label, ui.item.value);
			return false;
		}
	});
}
function add_article_slide(modrow, title, article_id){
	slideset = $('td[modrow="'+modrow+'"]');
	md_row = slideset.children().length +1;
	
	html = '<div class="slide_data" slidenum="' + md_row + '">';
	html += '	<div class="slide_article_title">' + title + '</div><br />';
	html += '	<input type="hidden" name="dn_carousel_module[' + modrow + '][data][' + md_row + '][article_id]" value="' + article_id + '" />';
	html += '<div class="image">';
	html += '<img src="<?= $no_image; ?>" alt="" id="thumb' + modrow +'-'+ md_row +'" />';
	html += '<input type="hidden" name="dn_carousel_module[' + modrow + '][data][' + md_row + '][image]" value="" id="image' + modrow + '-' + md_row + '"	/>';
	html += '<br />'
	html += "<a onclick=\"upload_image('image" + modrow + '-' + md_row + "', 'thumb" + modrow + '-' + md_row + "');\"><?= $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp";
	html += "<a onclick=\"$('#thumb" + modrow + '-' + md_row + "').attr('src', '<?= $no_image; ?>'); $('#image" + modrow + '-' + md_row + "').attr('value', '');\"><?= $text_clear; ?></a>";
	html += '</div>';
	html += '<br/>';
	html += '	<label><?= $entry_title_text; ?></label>';
	html += '	<input type="text" name="dn_carousel_module[' + modrow + '][data][' + md_row + '][display_title]" value="' + title + '" /><br />';
	html += '	<label><?= $entry_description; ?></label>';
	html += '	<textarea name="dn_carousel_module[' + modrow + '][data][' + md_row + '][description]"></textarea>';
	html += '	<img class="remove_slide_img" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" alt="" onclick="remove_article_slide(' + modrow + ',' + md_row + ');" />';
	html += '</div>';
	
	slideset.append(html);
}

function remove_article_slide(modrow,slidenum){
	$('#tab-module-' + modrow + ' div[slidenum="' + slidenum + '"]').remove();
	$('#tab-module-' + modrow + ' .slide_data').each(function(i,ele){
		$(this).attr('slidenum',i+1);
		$(this).find('input').each(function(i,e){$(e).attr('value', $(e).attr('value'));});
		$(this).find('textarea').each(function(i,e){$(e).html($(e).attr('value'));});
		$(this).find('.remove_slide_img').attr('onclick','remove_article_slide(' + modrow + ',' + (i+1) + ')');
		regex = new RegExp("\\[data\\]\\[.\\]","gi");
		regex2 = new RegExp("thumb\\d+-\\d+",'gi');
		regex3 = new RegExp("image\\d+-\\d+",'gi');
		$(this).html($(this).html().replace(regex,"[data][" + (i+1) +"]").replace(regex2,"thumb"+modrow+'-'+(i+1)).replace(regex3,"image"+modrow+'-'+(i+1)));
	});
}

$('#article-related div img').live('click', function() {
	$(this).parent().remove();
	
	$('#article-related div:odd').attr('class', 'odd');
	$('#article-related div:even').attr('class', 'even');
});
//--></script>
<script type="text/javascript"><!--
function image_upload(field, thumb) {
	$('#dialog').remove();
	
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
		title: '<?= $text_image_manager; ?>',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
				$.ajax({
					url: "<?= HTTP_ADMIN . "index.php?route=common/filemanager/image"; ?>" + '&image=' + encodeURIComponent($('#' + field).attr('value')),
					dataType: 'text',
					success: function(data) {
						$('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
					}
				});
			}
		},
		bgiframe: false,
		width: 700,
		height: 400,
		resizable: false,
		modal: false
	});
};
//--></script>
<script type="text/javascript"><!--
<? $module_row = 1; ?>
<? foreach ($modules as $module) { ?>
$('#language-<?= $module_row; ?> a').tabs();
<? $module_row++; ?>
<? } ?>
//--></script>
<?= $footer; ?>