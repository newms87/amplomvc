<div id='the_cart_form'>
<? if($ajax_cart) {
   echo $this->builder->display_messages($messages); 
} ?>
<?= $this->builder->display_errors($errors);?>
<? if(isset($no_price_display)){?>
<span id='cart_no_price_display'><?= $no_price_display;?></span>
<? }?>
<form id='cart_form' action="<?= $action;?>" method="post" enctype="multipart/form-data">
 <input type="hidden" name="cart_form" value="1" />
 <div class="cart-info">
   <table>
     <thead>
       <tr>
         <td class="image"><?= $column_image; ?></td>
         <td class="name"><?= $column_name; ?></td>
         <td class="model"><?= $column_model; ?></td>
         <td class="quantity"><?= $column_quantity; ?></td>
         <td class="finalsale"><?= $column_final_sale; ?></td>
         <? if(!isset($no_price_display)){ ?>
         <td class="price"><?= $column_price; ?></td>
         <td class="total"><?= $column_total; ?></td>
         <? }?>
       </tr>
     </thead>
     <tbody>
       <? foreach ($products as $product) { ?>
       <tr>
         <td class="image">
           <? if ($product['thumb']) { ?>
           <a href="<?= $product['href']; ?>">
              <img src="<?= $product['thumb']; ?>" alt="<?= $product['name']; ?>" title="<?= $product['name']; ?>" />
           </a>
           <? } ?>
         </td>
         <td class="name">
           <a href="<?= $product['href']; ?>"><?= $product['name']; ?></a>
           <? if (!$product['stock']) { ?>
           <span class="stock">***</span>
           <? } ?>
           <div>
             <? foreach ($product['option'] as $option) { ?>
             <div class='cart_product_option_value'><?= $text_option_bullet;?><?= $option['display_name']; ?>: <?= $option['value']; ?></div>
             <? } ?>
           </div>
           <? if ($product['reward']) { ?>
           <span class='cart_product_reward'><?= $product['reward']; ?></span>
           <? } ?>
         </td>
         <td class="model"><?= $product['model']; ?></td>
         <td class="quantity">
           <input type="text" name="quantity[<?= $product['key']; ?>]" value="<?= $product['quantity']; ?>" size="1" />
           <input type="image" <?= $ajax_cart ? 'onclick="handle_ajax_cart_update(); return false;"' : '';?> name='action' value='update' src="<?= HTTP_THEME_IMAGE . 'update.png'; ?>" alt="<?= $button_update; ?>" title="<?= $button_update; ?>" id='cart_update'/>
           <label for='cart_update'><?=$text_update_cart;?></label>
           <input type='image' <?= $ajax_cart ? 'onclick="handle_ajax_cart_remove($(this)); return false;"' : '';?> name='action' value="remove<?= $product['key'];?>" src="<?= HTTP_THEME_IMAGE . 'remove.png'; ?>" alt="<?= $button_remove; ?>" title="<?= $button_remove; ?>" />
         </td>
         <td class='final_sale_item'>
         <? if($product['is_final']){;?>
            <div class='extra_info_block'><span class='final_sale'></span><span class='help_icon'>?<span class='help_icon_popup'><?=$final_sale_explanation;?></span></span></div>
         <? }?>
         </td>
         <? if(!isset($no_price_display)){ ?>
         <td class="price"><?= $product['price']; ?></td>
         <td class="total"><?= $product['total']; ?></td>
         <? }?>
       </tr>
       <? } ?>
       <? foreach ($vouchers as $key => $voucher) { ?>
       <tr>
         <td class="image"></td>
         <td class="name"><?= $voucher['description']; ?></td>
         <td class="model"></td>
         <td class="quantity">
            <input type="text" name="" value="1" size="1" disabled="disabled" />
            <a href="<?= $voucher['remove']; ?>">
               <img src="<?= HTTP_THEME_IMAGE . 'remove.png'; ?>" alt="<?= $text_remove; ?>" title="<?= $button_remove; ?>" />
            </a>
         </td>
         <? if(!isset($no_price_display)){ ?>
         <td class="price"><?= $voucher['amount']; ?></td>
         <td class="total"><?= $voucher['amount']; ?></td>
         <? }?>
       </tr>
       <? } ?>
     </tbody>
   </table>
 </div>
</form>
</div>

<? if($ajax_cart) { ?>
<script type="text/javascript">//<!--
function handle_ajax_cart_update(){
   form = $('form#cart_form');
   
   data = form.find('input[value=update], select, input:checked, input[type="text"], input[type="hidden"], textarea');
   
   if(typeof handle_ajax_cart_preload == 'function'){
      handle_ajax_cart_preload('update', data);
   }
   
   $('#the_cart_form').load(form.attr('action'), data, 
      function(){
         if(typeof handle_ajax_cart_load == 'function'){
            handle_ajax_cart_load('update', data);
         }
      });
}

function handle_ajax_cart_remove(remove){
   form = $('form#cart_form');
   
   data = remove.add('[name=cart_form]');
   
   if(typeof handle_ajax_cart_preload == 'function'){
      handle_ajax_cart_preload('remove', data);
   }
   
   $('#the_cart_form').load(form.attr('action'), data,
      function(){
         if(typeof handle_ajax_cart_load == 'function'){
            handle_ajax_cart_load('remove', data);
         }
      });
}
//--></script>

<? } ?>