<form id="cart_shipping" action="<?= $action;?>" class="content">
   <p><?= $text_shipping_detail; ?></p>
   <table>
    <tr>
      <td><span class="required">*</span> <?= $entry_country; ?></td>
      <td>
         <? $this->builder->set_config('country_id', 'name');?>
         <?= $this->builder->build('select', $countries, "country_id", $country_id, array('class'=>'country_select'));?>
      </td>
    </tr>
    <tr>
      <td><span class="required">*</span> <?= $entry_zone; ?></td>
      <td><select name="zone_id" class="zone_select" zone_id="<?= $zone_id;?>"></select></td>
    </tr>
     <tr>
       <td><span class="required">*</span> <?= $entry_postcode; ?></td>
       <td><input type="text" name="postcode" value="<?= $postcode; ?>" /></td>
     </tr>
   </table>
   <input type="button" value="<?= $button_quote; ?>" id="button-quote" class="button" />
</form>

<div id='shipping_quote' style='display:none'>
   <h2><?= $text_shipping_method; ?></h2>
   <form action="<?= $apply_shipping; ?>" method="post" enctype="multipart/form-data">
      <table class="radio"></table>
      <input type="hidden" name="next" value="shipping" />
      <input type="hidden" name="redirect" value="<?= $redirect;?>" />
      <input type="submit" value="<?= $button_shipping; ?>" class="button" />    
   </form>
</div>

<?= $this->builder->js('load_zones', '#cart_shipping', '.country_select','.zone_select');?>

<script type="text/javascript">
//<!--
$('#button-quote').live('click', function() {
   $.ajax({
      url: 'index.php?route=cart/block/shipping/quote',
      type: 'post',
      data: $('#cart_shipping').serialize(),
      dataType: 'json',    
      beforeSend: function() {
         $('#button-quote').attr('disabled', true);
         $('#button-quote').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
      },
      complete: function() {
         $('#button-quote').attr('disabled', false);
         $('.wait').remove();
      },    
      success: function(json) {
         $('.success, .warning, .attention, .error').remove();       
                  
         if (json['error']) {
            if (json['error']['warning']) {
               display_notification('warning', json['error']['warning']);
            }
            
            for(var e in json['error']){
               $('select[name=' + e + ']').after('<span class="error">' + json['error'][e] + '</span>');
            }
         }
         
         if (json['shipping_method']) {
            $('#shipping_quote table').html('');
            
            for (i in json['shipping_method']) {
               html = '<tr>';
               html += '  <td colspan="3"><b>' + json['shipping_method'][i]['title'] + '</b></td>';
               html += '</tr>';
            
               if (!json['shipping_method'][i]['error']) {
                  for (j in json['shipping_method'][i]['quote']) {
                     html += '<tr class="highlight">';
                     
                     checked = json['shipping_method'][i]['quote'][j]['code'] == '<?= $shipping_method; ?>' ? 'checked="checked"' : '';
                     html += '<td><input type="radio" name="shipping_method" value="' + json['shipping_method'][i]['quote'][j]['code'] + '" id="' + json['shipping_method'][i]['quote'][j]['code'] + '" ' + checked + ' /></td>';
                        
                     html += '  <td><label for="' + json['shipping_method'][i]['quote'][j]['code'] + '">' + json['shipping_method'][i]['quote'][j]['title'] + '</label></td>';
                     html += '  <td style="text-align: right;"><label for="' + json['shipping_method'][i]['quote'][j]['code'] + '">' + json['shipping_method'][i]['quote'][j]['text'] + '</label></td>';
                     html += '</tr>';
                  }     
               } else {
                  html += '<tr>';
                  html += '  <td colspan="3"><div class="error">' + json['shipping_method'][i]['error'] + '</div></td>';
                  html += '</tr>';                 
               }
               
               $('#shipping_quote table').append(html);
            }
            
            $.colorbox({
               overlayClose: true,
               opacity: 0.5,
               width: '600px',
               height: '400px',
               href: false,
               html: $('#shipping_quote').clone().show()
            });
         }
      }
   });
});
//--></script> 