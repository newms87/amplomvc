<div class="box">
  <div class="box-heading"><?= $heading_title; ?></div>
  <div class="box-content">
    <div class="box-product">
      <? foreach ($products as $product) { ?>
      <div>
        <? if ($product['thumb']) { ?>
        <div class="image"><a href="<?= $product['href']; ?>"><img src="<?= $product['thumb']; ?>" alt="<?= $product['name']; ?>" /></a></div>
        <? } ?>
        <div class="name"><a href="<?= $product['href']; ?>"><?= $product['name']; ?></a></div>
        <? if ($product['price']) { ?>
        <div class="price">
          <? if (!$product['special']) { ?>
          <?= $product['price']; ?>
          <? } else { ?>
          <span class="price-old"><?= $product['price']; ?></span> <span class="price-new"><?= $product['special']; ?></span>
          <? } ?>
        </div>
        <? } ?>
        <? if ($product['rating']) { ?>
        <div class="rating"><img src="<?= HTTP_THEME_IMAGE . "stars-$product[rating].png"; ?>" alt="<?= $product['reviews']; ?>" /></div>
        <? } ?>
        <div class="cart"><input type="button" value="<?= $button_cart; ?>" onclick="addToCart('<?= $product['product_id']; ?>');" class="button" /></div>
      </div>
      <? } ?>
    </div>
  </div>
</div>
