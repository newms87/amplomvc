<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <? if ($error_warning) { ?>
  <div class="message_box warning"><?= $error_warning; ?></div>
  <? } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="<?= HTTP_THEME_IMAGE . 'country.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?= $entry_name; ?></td>
            <td><input type="text" name="name" value="<?= $name; ?>" />
              <? if ($error_name) { ?>
              <span class="error"><?= $error_name; ?></span>
              <? } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?= $entry_description; ?></td>
            <td><input type="text" name="description" value="<?= $description; ?>" />
              <? if ($error_description) { ?>
              <span class="error"><?= $error_description; ?></span>
              <? } ?></td>
          </tr>
        </table>
        <br />
        <table id="zone-to-geo-zone" class="list">
          <thead>
            <tr>
              <td class="left"><?= $entry_country; ?></td>
              <td class="left"><?= $entry_zone; ?></td>
              <td></td>
            </tr>
          </thead>
          <tbody>
          <? $zone_to_geo_zone_row = 0; ?>
          <? foreach ($zone_to_geo_zones as $zone_to_geo_zone) { ?>
            <tr class='geozone_selector'>
              <td class="left">
                 <? $this->builder->set_config('country_id', 'name');?>
                 <?= $this->builder->build('select', $countries, "zone_to_geo_zone[$zone_to_geo_zone_row][country_id]", $zone_to_geo_zone['country_id'], array('class'=>'country_selector'));?>
                 <a onclick="add_all_zones($(this))" style="text-decoration:none; display:block"><?= $button_add_all_zones;?></a>
              </td>
              <td class="left">
                 <select name="zone_to_geo_zone[<?= $zone_to_geo_zone_row; ?>][zone_id]" zone_id="<?= $zone_to_geo_zone['zone_id'];?>" class='zone_selector'></select>
              </td>
              <td class="left"><a onclick="$(this).closest('.geozone_selector').remove();" class="button"><?= $button_remove; ?></a></td>
            </tr>
          <? $zone_to_geo_zone_row++; ?>
          <? } ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="2"></td>
              <td class="left"><a onclick="addGeoZone();" class="button"><?= $button_add_geo_zone; ?></a></td>
            </tr>
          </tfoot>
        </table>
      </form>
    </div>
  </div>
</div>

<?= $this->builder->js('load_zones', '.geozone_selector', '.country_selector', '.zone_selector', true);?>

<script type="text/javascript">//<!--
function add_all_zones(context){
   country_id = context.closest('.geozone_selector').find('.country_selector').val();
   zone_id = context.closest('.geozone_selector').find('.zone_selector').val();
   zones = context.closest('.geozone_selector').find('.zone_selector').children();
   
   context.hide().fadeIn(600);
   
   if(zones.length < 1) return;
   
   zones.each(function(i,e){
      add = true;
      value = $(e).attr('value');
      
      $('.zone_selector').each(function(j,z){
         if($(z).val() == value){
            add = false;
            return false;
         }
      });
      
      if(add && value != zone_id && value != '0' && value){
         addGeoZone(country_id, value);
      }
   });
}
//--></script>

<script type="text/javascript">//<!--
var geozone_row = <?= $zone_to_geo_zone_row; ?>;

function addGeoZone(country_id, zone_id) {
   zone_id = zone_id || '';
   country_id = country_id || 0;
   
	html  = '<tr class="geozone_selector">';
   <? $this->builder->set_config('country_id', 'name');?>
	html += '  <td class="left">' + "<?= $this->builder->build('select', $countries, "zone_to_geo_zone[%geozone_row%][country_id]", '', array('class'=>'country_selector'), true);?>" + '<a onclick="add_all_zones($(this))" style="text-decoration:none;display:block;"><?= $button_add_all_zones;?></a></td>';
	html += '  <td class="left"><select name="zone_to_geo_zone[%geozone_row%][zone_id]" class="zone_selector" zone_id="' + zone_id + '"></select></td>';
	html += '  <td class="left"><a onclick="$(this).closest(\'.geozone_selector\').remove();" class="button"><?= $button_remove; ?></a></td>';
	html += '</tr>';
	
	$('#zone-to-geo-zone tbody').append(html.replace(/%geozone_row%/g, geozone_row));
	
	$('.geozone_selector').last().find('.country_selector').val(country_id).trigger('change');
	
	geozone_row++;
}
//--></script> 
<?= $footer; ?>