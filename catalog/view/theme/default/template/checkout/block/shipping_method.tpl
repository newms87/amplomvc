<? if(isset($no_shipping_address)) {?>
   <h2><?= $no_shipping_address;?></h2>
<? } else{ ?>
   <?= $this->builder->display_errors($errors);?>
   <? if ($shipping_methods) { ?>
   <table class="radio">
   <? foreach ($shipping_methods as $shipping_method) { ?>
     <tr>
       <td colspan="3"><b><?= $shipping_method['title']; ?></b></td>
     </tr>
     <? if (!$shipping_method['error']) { ?>
        <? foreach ($shipping_method['quote'] as $quote) { ?>
        <tr class="highlight">
          <td>
            <input type="radio" name="shipping_method" value="<?= $quote['code']; ?>" id="<?= $quote['code']; ?>" <?= $quote['code'] == $code ? 'checked="checked"' : '';?> />
          </td>
          <td><label for="<?= $quote['code']; ?>"><?= $quote['title']; ?></label></td>
          <td style="text-align: right;"><label for="<?= $quote['code']; ?>"><?= $quote['text']; ?></label></td>
        </tr>
        <? } ?>
     <? } else{ ?>
        <tr>
          <td colspan="3"><div class="error"><?= $shipping_method['error']; ?></div></td>
        </tr>
     <? } ?>
   <? } ?>
   </table>
   <? } else { ?>
      <h2><?= $text_zone_allowed;?></h2>
      <div class='allowed_zone_list'>
      <? foreach($allowed_geo_zones as $i=>$geo_zone){ ?>
         <span class='allowed_zone_item'><?= $geo_zone['country']['name'] . (($i==count($allowed_geo_zones)-1) ? '' : $text_zone_separator);?></span>
      <? } ?>
      </div>
   <? }?>
<? }?>

<script type="text/javascript">//<!--
$('#shipping_method').bind('loaded', function (){
   <? if($code && !$guest_checkout){ ?>
   if($('#shipping_address').hasClass('valid')){
      validate_info_item('shipping_method');
   }
   <? }?>
});
//--></script> 