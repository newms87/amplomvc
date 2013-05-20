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
      if(count($args) < 3 || count($args) > 4){
         trigger_error("Template JS: load_zones: accepts 3 or 4 arguments! Usage: \$this->builder->js('load_zones', \$parent_selector,\$country_selector,\$zone_selector[,\$allow_all]);");
         return '';
      }
      $parent_selector = $args[0];
      $country_selector = $args[1];
      $zone_selector = $args[2];
      
      $allow_all = (count($args) > 3 && $args[3]) ? "&allow_all" : "";
   ?>
<script type="text/javascript">//<!--
country_selectors = $('<?= $parent_selector;?>').find('<?= $country_selector;?>');

country_selectors.live('change', function(event){
  cs = $(this);
  
  if(!$(this).is('select')){
     cs = cs.find('<?= $country_selector;?>');
  }

  if(!cs.val()) return;
  
  zone_selector = cs.closest('<?= $parent_selector;?>').find('<?=$zone_selector;?>');
  
  if(zone_selector.attr('country_id') == cs.val()) return;
  
  zone_selector.attr('country_id', cs.val());
  
  zone_selector.attr('zone_id', zone_selector.val() ||  zone_selector.attr('zone_id') || zone_selector.attr('select_value') || 0);
   
  zone_selector.load('index.php?route=tool/data/load_zones&country_id=' + cs.val() + '<?=$allow_all;?>', 
    function(){
      zs = $(this).closest('<?= $parent_selector;?>').find('<?= $zone_selector;?>');
      zs.val(zs.attr('zone_id') || 0).trigger('change');
    });
});

country_selectors.each(function(i,e){
   zs = $(e).closest('<?= $parent_selector;?>').find('<?=$zone_selector;?>');
   if(zs.children().length < 1 || !zs.val()){
      $(e).trigger('change', $(e));
   }
});
//--></script>
<?     break;

/**
 * upload_image
 * 
 * Makes the function upload_image available which displays the image upload manager 
 * with a callback to a image input field(s).
 */

	case 'image_manager': ?>
<? if(!isset($js_loaded_files['image_manager'])) { ?>
<script type="text/javascript">//<!--
var image_manager_url = "<?= HTTP_ROOT . "index.php?route=common/filemanager"; ?>";
//--></script>
<script type="text/javascript" src="<?= HTTP_JS . "image_manager.js"; ?>"></script>
<? } ?>
	<? break;
	
	
/**
 * filter_url
 * 
 * provides a filter() function that will load the specified $route and generating filter arguments in the query
 * 
 * @param $route - The route of the page to load
 * @param $filters - The list of filters to activate
 */
   case 'filter_url':
      if(count($args) < 2 || count($args) > 3){
         trigger_error("Template JS: filter_url: excepts exactly 1 argument! Usage: \$this->builder->js('filter_url', selector, route, query='');");
         return '';
      }
      
      $selector = $args[0];
      $url = $args[1];
      $query = isset($args[2]) ? $args[2] : '';
      
      $sort_query = $this->url->get_query("sort","order","page");
   ?>
<script type="text/javascript">//<!--
function filter() {
   url = '<?= HTTP_ROOT; ?>index.php?route=<?= $url . '&' . $sort_query . ($query ? '&'.$query : '');?>';
   
   filter_list = $('<?= $selector;?> [name]').not('[value=""]');
   
   if(filter_list.length){
      url += '&' + filter_list.serialize();
   }
   
   location = url;
}
//--></script> 
<?     break;


   case 'datepicker': ?>
<? if(!isset($js_loaded_files['datepicker'])) {?>
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
   context.after("<span class='error'>"+errors[e]+"</span");
}
//--></script>

<?php break;


   case 'autocomplete': 
      if(!$args || count($args) < 3){
         trigger_error("Template JS: autocomplete: invalid arguments! Usage: \$this->builder->js('autocomplete', array(\$selector,\$label,\$value,\$callback));");
         return '';
      }
      $selector = $args[0];
      $label = $args[1];
      $value = $args[2];
      $callback = $args[3];
		
		$sort_query = $this->url->get_query("sort","limit");
   ?>
<script type="text/javascript">//<!--
$('<?=$selector;?>').each(function(i,e){
   $(e).autocomplete({
      delay: 0,
      source: function(request, response) {
      	filter = {};
      	filter[$('<?= $selector; ?>').attr('filter')] = request.term;
		   
         $.ajax({
            url: '<?= HTTP_ROOT; ?>index.php?route=' + $(e).attr('route') + '&<?= $sort_query; ?>',
            dataType: 'json',
            data: {filter: filter},
            success: function(json) {
               response($.map(json, function(item) {
                  item['label'] = item.<?= $label;?>;
                  item['value'] = item.<?= $value;?>;
                  
                  return item;
               }));
            }
         });
      }, 
      select: function(event, ui) {
         <?= $callback;?>($(e), ui.item);
         
         return false;
      }
   })
});
//--></script> 
<?php break;


   case 'ckeditor': ?>
<? if(!isset($js_loaded_files['ckeditor'])) { ?>
<script type="text/javascript" src="<?= HTTP_JS .'ckeditor/ckeditor.js'; ?>"></script>
<script type="text/javascript">//<!--
ckedit_index = Math.random() * 9000 + 1000;
$('.ckedit').each(function(i,e){
   if(!$(e).attr('id')){
      $(e).attr('id','ckedit' + ckedit_index++);
   }
   init_ckeditor_for($(e).attr('id'));
});
function init_ckeditor_for(id){
   if(!id)throw new Error("CKEDITOR: You must include an ID on elements using the CKEDITOR");
   ck = CKEDITOR.replace(id, {
      filebrowserBrowseUrl: 'index.php?route=common/filemanager/ckeditor',
      filebrowserImageBrowseUrl: 'index.php?route=common/filemanager/ckeditor',
      filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager/ckeditor',
      filebrowserUploadUrl: 'index.php?route=common/filemanager/ckeditor',
      filebrowserImageUploadUrl: 'index.php?route=common/filemanager/ckeditor',
      filebrowserFlashUploadUrl: 'index.php?route=common/filemanager/ckeditor'
   });
}
function remove_ckeditor_for(id){
   CKEDITOR.instances[id].destroy();
}
//--></script>
<? } ?>
<?php break;


   case 'html_entity_decode': ?>

<script type="text/javascript">//<!--
function html_entity_decode (string, quote_style) {
    var hash_map = {},
        symbol = '',
        tmp_str = '',
        entity = '';
    tmp_str = string.toString();

    if (false === (hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style))) {
        return false;
    }
    delete(hash_map['&']);
    hash_map['&'] = '&amp;';

    for (symbol in hash_map) {
        entity = hash_map[symbol];
        tmp_str = tmp_str.split(entity).join(symbol);
    }
    tmp_str = tmp_str.split('&#039;').join("'");

    return tmp_str;
}
function get_html_translation_table (table, quote_style) {
    var entities = {},
        hash_map = {},
        decimal;
    var constMappingTable = {},
        constMappingQuoteStyle = {};
    var useTable = {},
        useQuoteStyle = {};

    // Translate arguments
    constMappingTable[0] = 'HTML_SPECIALCHARS';
    constMappingTable[1] = 'HTML_ENTITIES';
    constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
    constMappingQuoteStyle[2] = 'ENT_COMPAT';
    constMappingQuoteStyle[3] = 'ENT_QUOTES';

    useTable = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
    useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';

    if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
        throw new Error("Table: " + useTable + ' not supported');
    }

    entities['38'] = '&amp;';
    if (useTable === 'HTML_ENTITIES') {
        entities['160'] = '&nbsp;';
        entities['161'] = '&iexcl;';
        entities['162'] = '&cent;';
        entities['163'] = '&pound;';
        entities['164'] = '&curren;';
        entities['165'] = '&yen;';
        entities['166'] = '&brvbar;';
        entities['167'] = '&sect;';
        entities['168'] = '&uml;';
        entities['169'] = '&copy;';
        entities['170'] = '&ordf;';
        entities['171'] = '&laquo;';
        entities['172'] = '&not;';
        entities['173'] = '&shy;';
        entities['174'] = '&reg;';
        entities['175'] = '&macr;';
        entities['176'] = '&deg;';
        entities['177'] = '&plusmn;';
        entities['178'] = '&sup2;';
        entities['179'] = '&sup3;';
        entities['180'] = '&acute;';
        entities['181'] = '&micro;';
        entities['182'] = '&para;';
        entities['183'] = '&middot;';
        entities['184'] = '&cedil;';
        entities['185'] = '&sup1;';
        entities['186'] = '&ordm;';
        entities['187'] = '&raquo;';
        entities['188'] = '&frac14;';
        entities['189'] = '&frac12;';
        entities['190'] = '&frac34;';
        entities['191'] = '&iquest;';
        entities['192'] = '&Agrave;';
        entities['193'] = '&Aacute;';
        entities['194'] = '&Acirc;';
        entities['195'] = '&Atilde;';
        entities['196'] = '&Auml;';
        entities['197'] = '&Aring;';
        entities['198'] = '&AElig;';
        entities['199'] = '&Ccedil;';
        entities['200'] = '&Egrave;';
        entities['201'] = '&Eacute;';
        entities['202'] = '&Ecirc;';
        entities['203'] = '&Euml;';
        entities['204'] = '&Igrave;';
        entities['205'] = '&Iacute;';
        entities['206'] = '&Icirc;';
        entities['207'] = '&Iuml;';
        entities['208'] = '&ETH;';
        entities['209'] = '&Ntilde;';
        entities['210'] = '&Ograve;';
        entities['211'] = '&Oacute;';
        entities['212'] = '&Ocirc;';
        entities['213'] = '&Otilde;';
        entities['214'] = '&Ouml;';
        entities['215'] = '&times;';
        entities['216'] = '&Oslash;';
        entities['217'] = '&Ugrave;';
        entities['218'] = '&Uacute;';
        entities['219'] = '&Ucirc;';
        entities['220'] = '&Uuml;';
        entities['221'] = '&Yacute;';
        entities['222'] = '&THORN;';
        entities['223'] = '&szlig;';
        entities['224'] = '&agrave;';
        entities['225'] = '&aacute;';
        entities['226'] = '&acirc;';
        entities['227'] = '&atilde;';
        entities['228'] = '&auml;';
        entities['229'] = '&aring;';
        entities['230'] = '&aelig;';
        entities['231'] = '&ccedil;';
        entities['232'] = '&egrave;';
        entities['233'] = '&eacute;';
        entities['234'] = '&ecirc;';
        entities['235'] = '&euml;';
        entities['236'] = '&igrave;';
        entities['237'] = '&iacute;';
        entities['238'] = '&icirc;';
        entities['239'] = '&iuml;';
        entities['240'] = '&eth;';
        entities['241'] = '&ntilde;';
        entities['242'] = '&ograve;';
        entities['243'] = '&oacute;';
        entities['244'] = '&ocirc;';
        entities['245'] = '&otilde;';
        entities['246'] = '&ouml;';
        entities['247'] = '&divide;';
        entities['248'] = '&oslash;';
        entities['249'] = '&ugrave;';
        entities['250'] = '&uacute;';
        entities['251'] = '&ucirc;';
        entities['252'] = '&uuml;';
        entities['253'] = '&yacute;';
        entities['254'] = '&thorn;';
        entities['255'] = '&yuml;';
    }

    if (useQuoteStyle !== 'ENT_NOQUOTES') {
        entities['34'] = '&quot;';
    }
    if (useQuoteStyle === 'ENT_QUOTES') {
        entities['39'] = '&#39;';
    }
    entities['60'] = '&lt;';
    entities['62'] = '&gt;';


    // ascii decimals to real symbols
    for (decimal in entities) {
        if (entities.hasOwnProperty(decimal)) {
            hash_map[String.fromCharCode(decimal)] = entities[decimal];
        }
    }

    return hash_map;
}
//--></script>
<?php break;


   default:
      break;
}

$js_loaded_files[$js] = true;