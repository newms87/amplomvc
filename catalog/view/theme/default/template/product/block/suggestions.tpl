<div id='suggested_products'>
   <? foreach($suggestions as $s){?>
      <a href='<?= $s['href'];?>' class='suggested_product'>
        <? if ($s['thumb']) { ?>
        <div class="image"><img src="<?= $s['thumb']; ?>" alt="<?= $s['name']; ?>" /></div>
        <? } ?>
        <div class='suggested_product_info'>
           <div class="name"><?= $s['name']; ?></div>
           <? if ($s['price']) { ?>
           <div class="price">
             <? if (!$s['special']) { ?>
             <span class="price-new"><?= $s['price']; ?></span>
             <? } else { ?>
            <span class="price-new"><?= $s['special']; ?></span> <span class="price-old"><?= $s['price']; ?> <?=$text_retail;?></span>
             <? } ?>
           </div>
           <? } ?>
           <? if($s['flashsale_id']){?>
            <div class='fs_countdown'><div id='fpop-<?=$s['product_id'];?>' class='flash_countdown' callback='end_featured_fs' type='short' flashid='<?=$s['flashsale_id'];?>'></div></div>
           <? }?>
        </div>
      </a>
   <? } ?>
</div>