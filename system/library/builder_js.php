<?php

switch($js){

/**
 * load_zones
 *
 * Loads the option values for a html select into $zone_selector, based on the current $country_selector value
 *
 * @param $country_selector - The jQuery selector for the country select element
 * @param $zone_selector - The jQuery selector for the zone select element
 * @param $parent_selector - The jQuery selector for the common parent element of both zone/country selectors
 */
	case 'load_zones':
		if (count($args) < 3 || count($args) > 4) {
			trigger_error("Template JS: load_zones: accepts 3 or 4 arguments! Usage: \$this->builder->js('load_zones', \$parent_selector,\$country_selector,\$zone_selector[,\$allow_all]);");
			return '';
		}
		$parent_selector = $args[0];
		$country_selector = $args[1];
		$zone_selector = $args[2];
		
		$url_load_zones = $this->url->link('tool/data/load_zones', ((count($args) > 3 && $args[3]) ? "allow_all" : "") . '&country_id=');
	?>
<script type="text/javascript">//<!--
country_selectors = $('<?= $parent_selector;?>').find('<?= $country_selector;?>');

country_selectors.live('change', function (event)
 {
  cs = $(this);

  if (!$(this).is('select')) {
	cs = cs.find('<?= $country_selector;?>');
  }

  if(!cs.val()) return;

  zone_selector = cs.closest('<?= $parent_selector;?>').find('<?=$zone_selector;?>');

  if(zone_selector.attr('country_id') == cs.val()) return;

  zone_selector.attr('country_id', cs.val());

  zone_selector.attr('zone_id', zone_selector.val() ||  zone_selector.attr('zone_id') || zone_selector.attr('select_value') || 0);
	
  zone_selector.load("<?= $url_load_zones;?>" + cs.val(),
	function ()
	{
		zs = $(this).closest('<?= $parent_selector;?>').find('<?= $zone_selector;?>');
		zs.val(zs.attr('zone_id') || 0).trigger('change');
	});
});

country_selectors.each(function (i,e) {
	zs = $(e).closest('<?= $parent_selector;?>').find('<?=$zone_selector;?>');
	if (zs.children().length < 1 || !zs.val()) {
		$(e).trigger('change', $(e));
	}
});
//--></script>
<?	break;

/**
 * upload_image
 *
 * Makes the function upload_image available which displays the image upload manager
 * with a callback to a image input field(s).
 */

	case 'image_manager': ?>
<? if (!isset($js_loaded_files['image_manager'])) { ?>
<script type="text/javascript">//<!--
var image_manager_url = "<?= $this->url->link('common/filemanager'); ?>";
var no_image = "<?= HTTP_THEME_IMAGE . "no_image.png"; ?>"
//--></script>
<script type="text/javascript" src="<?= HTTP_JS . "image_manager.js"; ?>"></script>
<? } ?>
	<? break;
	

	case 'datepicker': ?>
<? if (!isset($js_loaded_files['datepicker'])) {?>
<script type="text/javascript" src="<?= HTTP_JS . 'jquery/ui/jquery-ui-timepicker-addon.js'; ?>"></script>
<script type="text/javascript">//<!--
$('.date').datepicker({dateFormat: 'yy-mm-dd'});
$('.datetime').datetimepicker({
	dateFormat: 'yy-mm-dd',
	timeFormat: 'h:m:s'
});
$('.time').timepicker({timeFormat: 'h:m'});
//--></script>
<? } ?>
<?php  break;
		
		
	case 'errors':
		if(!$args)return'';
		$errors = json_encode($args[0]); ?>
		
<script type="text/javascript">//<!--
var errors = <?=$errors;?>;
for(var e in errors){
	context = $('#content [name="'+e+'"]');
	if(!context.length)
		context = $('#'+e);
	if(!context.length)
		context = $(e);
	context.after("<span class ='error'>"+errors[e]+"</span");
}
//--></script>

<?php break;



	case 'ckeditor': ?>
<? if (!isset($js_loaded_files['ckeditor'])) { ?>
<script type="text/javascript" src="<?= HTTP_JS .'ckeditor/ckeditor.js'; ?>"></script>
<script type="text/javascript">//<!--
var ckedit_index = 0;

function init_ckeditor_for(context){
	context.each(function (i,e){
		if (!$(e).attr('id') || CKEDITOR.instances[$(e).attr('id')]) {
			$(e).attr('id', 'ckedit_' + ckedit_index++);
		}
		
		CKEDITOR.replace($(e).attr('id'), {
			filebrowserBrowseUrl: "<?= HTTP_AJAX . 'common/filemanager/ckeditor'; ?>",
			filebrowserImageBrowseUrl: "<?= HTTP_AJAX . 'common/filemanager/ckeditor'; ?>",
			filebrowserFlashBrowseUrl: "<?= HTTP_AJAX . 'common/filemanager/ckeditor'; ?>",
			filebrowserUploadUrl: "<?= HTTP_AJAX . 'common/filemanager/ckeditor'; ?>",
			filebrowserImageUploadUrl: "<?= HTTP_AJAX . 'common/filemanager/ckeditor'; ?>",
			filebrowserFlashUploadUrl: "<?= HTTP_AJAX . 'common/filemanager/ckeditor'; ?>"
		});
	});
}

init_ckeditor_for($('.ckedit').not('.template'));

function remove_ckeditor_for(context) {
	context.each(function (i,e) {
		CKEDITOR.instances[$(e).attr('id')].destroy();
	});
}
//--></script>
<? } ?>
<?php break;

	case 'translations':
		if (empty($args[0])) {
			$args[0] = array();
		}
		
		if (!empty($args[1])) {
			$name_format = $args[1];
		} else {
			$name_format = false;
		}
		
		$languages = $this->System_Model_Language->getEnabledLanguages();
		
		$default_language = $this->config->get('config_language_id');
		
		$translations = json_encode($args[0]); ?>
		
<div id="language_menu_template">
	<div class ="language_menu">
	<? foreach($languages as $language) { ?>
		<div class ="language_item <?= $language['language_id'] == $default_language ? 'active' : ''; ?>" title="<?= $language['name']; ?>" lang_id="<?= $language['language_id']; ?>">
			<img alt="<?= $language['name']; ?>" src="<?= HTTP_THEME_IMAGE . "flags/$language[image]"; ?>" />
		</div>
	<? } ?>
	</div>
</div>

<script type="text/javascript">//<!--
$('.language_menu .language_item').click(function () {
	lang_id = $(this).attr('lang_id');
	$('.translation').hide();
	$('.translation.' + lang_id).show();
	$('.language_menu .language_item.active').removeClass('active');
	$('.language_menu [lang_id=' + lang_id +']').addClass('active');
});

language_menu = $('#language_menu_template .language_menu').clone(true);
$('#language_menu_template').remove();

var translations = <?= $translations; ?>;
var default_language = "<?= $default_language ?>";

for(var t in translations){
	<? if ($name_format) { ?>
		context = $('[name="<?= $name_format; ?>"]'.replace(/%name%/, t));
	<? } else { ?>
		context = $('[name="'+t+'"]');
	<? } ?>
	
	if (!context.length) {
		context = $(t);
	}
	
	if(!context.length) break;
	
	box = $('<div class ="translation_box" />');
	
	context.before(box);
	
	box.append(context);
	
	for(var lang in translations[t]){
		<? if ($name_format) { ?>
			t_name = "<?= $name_format; ?>".replace(/%name%/,"translations][" + t + "][" + lang + "");
		<? } else { ?>
			t_name = "translations[" + t + "][" + lang + "]";
		<? } ?>
		
		t_input = context.clone();		
		t_input.attr('name', t_name);
		t_input.val(translations[t][lang]);
		
		if (t_input.hasClass('ckedit')) {
			t_input.attr('id','translation_' + t + '_' + lang);
			
			box.append($('<div class ="translation ' + lang +'" />').append(t_input));
			
			<? if (isset($js_loaded_files['ckeditor'])) { ?>
				init_ckeditor_for($('#translation_' + t + '_' + lang));
			<? } ?>
		}
		else {
			t_input.addClass('translation ' + lang);
			box.append(t_input);
		}
	}
	
	box.append(language_menu.clone(true));
	
	if (context.hasClass('ckedit')) {
		ckedit_box = $('<div class ="translation ' + default_language + '" />');
		
		context.before(ckedit_box);
		ckedit_box.append(context);
		
		ckedit_box.show();
	}
	else {
		context.addClass('translation ' + default_language);
		context.show();
	}
}
//--></script>

<?php break;



	default:
		break;
}

$js_loaded_files[$js] = true;