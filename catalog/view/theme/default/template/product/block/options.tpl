<div id='product_options' class="options">
  <h2><?= $text_option; ?></h2>
  <br />
  <? foreach ($product_options as $option) { ?>
  <div id="option-<?= $option['product_option_id']; ?>" option_id='<?=$option['product_option_id'];?>' class="option">
     <? if ($option['required']) { ?>
    <span class="required"></span>
    <? } ?>
    <b><?= $option['display_name']; ?>:</b><br />
    
  <?=$this->builder->set_config('product_option_value_id','name');?>
  
  <? switch ($option['type']) {
       case 'select': ?>
         <select class='selected_option'>
         <? foreach($option['product_option_value'] as $option_value){?>
            <option ov="<?= $option_value['option_value_id'];?>" value="<?= $option_value['product_option_value_id'];?>"><?= $option_value['name'];?></option>
         <? }?>
         </select>
         <? break;
       
       case 'radio': ?>
       <?= $this->builder->build('radio', $option['product_option_value'],  "option[$option[product_option_id]]");?>
       <? break;
       
       case 'checkbox': ?>
       <? foreach ($option['product_option_value'] as $product_option_value) { ?>
       <input type="checkbox" name="option[<?= $option['product_option_id']; ?>][]" value="<?= $product_option_value['product_option_value_id']; ?>" id="option-value-<?= $product_option_value['product_option_value_id']; ?>" />
       <label for="option-value-<?= $product_option_value['product_option_value_id']; ?>"><?= $product_option_value['name']; ?></label>
       <br />
       <? } ?>
       <? break;
       
       case 'image': ?>
       <div class='option_image_list'>
       <? foreach($option['product_option_value'] as $product_option_value){ ?>
         <div class="option_image" onclick="select_me($(this));" ov="<?= $product_option_value['option_value_id'];?>" value="<?= $product_option_value['product_option_value_id'];?>" id="pov-<?= $product_option_value['product_option_value_id'];?>">
            <div class='option_image_box'>
            	<? if($product_option_value['thumb']) {?>
               <a href="javscript:void(0);" title="<?= $product_option_value['name']; ?>" rel="<?=$product_option_value['rel'];?>" >
                  <img src="<?= $product_option_value['thumb'];?>" />
               </a>
               <? } else { ?>
               	<a href="javscript:void(0);" title="<?= $product_option_value['name']; ?>">
	                  <img src="<?= $option_value_no_image;?>" />
	               </a>
               <? } ?>
            </div>
            <div class='option_image_name'><?=$product_option_value['name'];?></div>
         </div>
       <? }?>
       </div>
       <? break;
       default:
         break;
     } ?>
  </div>
  <? } ?>
</div>

<script type="text/javascript">//<!--

$('#product_options input, #product_options select').change(update_option_restrictions);

var restrictions = <?= json_encode($product_option_restrictions);?>;
function update_option_restrictions(){
   $('#product_options [ov]').removeAttr('disabled').removeClass('disabled');
   
   $('.selected_option').each(function(index,e){
      for(var i in restrictions){
         ov = 0;
         if($(e).is('select')){
            ov = parseInt($(e).find('option[value="'+$(e).val()+'"]').attr('ov'));
         }
         else{
            ov = parseInt($(e).attr('ov'));
         }
         
         if(i == ov){
            for(var r=0; r < restrictions[i].length; r++){
               ele = $('#product_options [ov="' + restrictions[i][r] + '"]');
               if(ele.is('option')){
                  ele.attr('disabled',1);
               }
               else{
                  ele.addClass('disabled');
               }
               
            }
         }
      }
   });
}

function select_me(context){
   if($(context).hasClass('disabled')) return;
   
   context.closest('.option_image_list').find('.option_image').removeClass('selected_option');
   context.addClass('selected_option');
   
   update_option_restrictions(parseInt(context.attr('ov')));
}
//--></script>
