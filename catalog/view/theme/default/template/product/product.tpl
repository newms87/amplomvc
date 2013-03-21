<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div id='content'>
  <?= $this->builder->display_breadcrumbs();?>
  <?= $content_top; ?>
  
  <h1><?= $heading_title; ?></h1>
  <? if(!empty($block_product_flashsale_countdown)){ ?>
     <?= $block_product_flashsale_countdown;?>
  <? }?>
  
  <div class="product-info">
    <?= $block_product_images;?>
    
    <?= $block_product_information;?>
  </div>
  
  <?= $block_product_additional;?>
  
  <? if(!empty($block_product_related)) {?>
  <?= $block_product_related;?>
  <? } ?>
  
  <? if ($tags) { ?>
  <div class="tags"><b><?= $text_tags; ?></b>
    <? foreach($tags as $i => $tag) {?>
    <a href="<?= $tags[$i]['href']; ?>"><?= $tags[$i]['tag']; ?></a> <?= $i == (count($tags) -1) ? '':',';?>
    <? } ?>
  </div>
  <? } ?>
  
  <?= $content_bottom; ?>
</div>

<?= $footer; ?>