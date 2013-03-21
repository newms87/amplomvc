<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div id="content">
  
  <?= $content_top; ?>
  
  <?= $this->builder->display_breadcrumbs();?>
  <h1><?= $heading_title; ?></h1>
  <? if ($products) { ?>
   <div class="box-product">
      <? foreach($products as $product){?>
         <? extract($product);?> 
         <a class='product_details' href='<?=$href;?>' type='<?= !empty($product['section_id'])?$product['section_id']:'';?>'>
            <div class='product_images'>
               <img src='<?=$thumb;?>' alt='<?=$name;?>' />
            </div>
            <div class='product_info'>
               <div class='product_info_title'><?=$name;?></div>
               <? if($special){ ?>
                  <div class='product_info_price'><?= $special;?></div>
                  <div class='product_info_orig_price'><?= $price;?> retail</div>
               <? }else{?>
                  <div class='product_info_price'><?=$price;?></div>
               <? }?>
               <? if(isset($flashsale_id) && $flashsale_id > 0){echo 'flashsale';?>
                  <div class='fs_countdown'><div class='flash_countdown' id='designer-prod-<?=$product_id;?>' callback='end_product_sale' flashid='<?=$flashsale_id;?>'></div></div>
               <? }?>
            </div>
            <div style='clear:both'></div>
         </a>
         <? }?>
   </div>
   <? } ?>
   <?= $content_bottom; ?>
   
</div>

<?= $footer;?>