<div class="box featured_box">
  <div class="box-heading"><div class='featured_title'><?= $heading_title; ?></div></div>
  <div class="box-content">
    <div class="box-product">
      <? foreach ($products as $product) { ?>
      <a href='<?= $product['href'];?>' class='featured_product_clickable'>
        <? if ($product['thumb']) { ?>
        <div class="image"><img src="<?= $product['thumb']; ?>" alt="<?= $product['name']; ?>" /></div>
        <? } ?>
        <div class='featured_product_info'>
           <div class="name"><?= $product['name']; ?></div>
           <? if ($product['price']) { ?>
           <div class="price">
             <? if (!$product['special']) { ?>
             <?= $product['price']; ?>
             <? } else { ?>
            <span class="price-new"><?= $product['special']; ?></span> <span class="price-old"><?= $product['price']; ?> retail</span>
             <? } ?>
           </div>
           <? } ?>
           <? if($product['flashsale_id']){?>
            <div class='fs_countdown'><div id='fpop-<?=$product['product_id'];?>' class='flash_countdown' callback='end_featured_fs' type='short' flashid='<?=$product['flashsale_id'];?>'></div></div>
           <? }?>
        </div>
      </a>
      <? } ?>
    </div>
  </div>
</div>
