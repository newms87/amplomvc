<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div class="content"><?= $content_top; ?>
  <?= $this->builder->display_breadcrumbs();?>
  <h1><?= $heading_title; ?></h1>
  <? if ($thumb || $description) { ?>
  <div class="category-info">
    <? if ($thumb) { ?>
    <div class="image"><img src="<?= $thumb; ?>" alt="<?= $heading_title; ?>" /></div>
    <? } ?>
    <? if ($description) { ?>
    <?= $description; ?>
    <? } ?>
  </div>
  <? } ?>
  
  <? if ($products) { ?>
  <div class="product-filter">
    <div class="display">
    	<span><?= $text_display; ?></span>
    	<?= $text_grid;?> <b>/</b> <a onclick="display('list');"><?= $text_list; ?></a>
    </div>
    <div class="limit">
    	<span><?= $text_limit; ?></span>
    	<? foreach($limits as $value => $limit_text){ ?>
    	<a <?= $value == $limit ? "class='selected'" : "href=\"$limit_url$value\"";?>><?= $limit_text;?></a>
    	<? } ?>
    </div>
    <div class="sort">
    	<span class="sort_text"><?= $text_sort; ?></span>
    	<?= $this->builder->build('select', $sorts, 'sort_list', $sort_select);?>
    </div>
  </div>
  <!--<div class="product-compare"><a href="<?= $compare; ?>" id="compare-total"><?= $text_compare; ?></a></div> -->
  <div id="product_list" class='grid'>
    <? foreach ($products as $product) { ?>
    <div class="product_block">
      <? if ($product['thumb']) { ?>
      <div class="image"><a href="<?= $product['href']; ?>"><img src="<?= $product['thumb']; ?>" title="<?= $product['name']; ?>" alt="<?= $product['name']; ?>" /></a></div>
      <? } ?>
      <div class="product_text">
	      <div class="name"><a href="<?= $product['href']; ?>"><?= $product['name']; ?></a></div>
	      <div class="description"><?= $product['description']; ?></div>
	   </div>
	   <? if ($product['price']) { ?>
	      <div class="price">
	        <? if (!$product['special']) { ?>
	        <?= $product['price']; ?>
	        <? } else { ?>
	        <span class="price-old"><?= $product['price']; ?></span> <span class="price-new"><?= $product['special']; ?></span>
	        <? } ?>
	        <? if ($product['tax']) { ?>
	        <br />
	        <span class="price-tax"><?= $text_tax; ?> <?= $product['tax']; ?></span>
	        <? } ?>
	      </div>
	      <? } ?>
	      <? if ($product['rating']) { ?>
	      <div class="rating"><img src="catalog/view/theme/default/image/stars-<?= $product['rating']; ?>.png" alt="<?= $product['reviews']; ?>" /></div>
      <? } ?>
      <div class="cart">
        <input type="button" value="<?= $button_cart; ?>" onclick="addToCart('<?= $product['product_id']; ?>');" class="button" />
      </div>
      <? /*
      <div class="wishlist"><a onclick="addToWishList('<?= $product['product_id']; ?>');"><?= $button_wishlist; ?></a></div>
      <div class="compare"><a onclick="addToCompare('<?= $product['product_id']; ?>');"><?= $button_compare; ?></a></div>
		 */?>
    </div>
    <? } ?>
  </div>
  <div class="pagination"><?= $pagination; ?></div>
  <? } ?>
  <? if (!$categories && !$products) { ?>
  <div class="content"><?= $text_empty; ?></div>
  <div class="buttons">
    <div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
  </div>
  <? } ?>
  <?= $content_bottom; ?></div>
<script type="text/javascript">
//<!--
$('[name=sort_list]').change(function(){
	location = '<?= $sort_url;?>' + '&' + $(this).val();
});

$('[name=show_limit]').change(function(){
	location = '<?= $limit_url;?>' + '&limit=' + $(this).val();
});

function display(view) {
	if (view == 'list') {
		$('#product_list').removeClass('grid').addClass('list');
		
		$('.display').html('<b><?= $text_display; ?></b> <?= $text_list; ?> <b>/</b> <a onclick="display(\'grid\');"><?= $text_grid; ?></a>');
		
		$.cookie('display', 'list'); 
	} else {
		$('#product_list').removeClass('list').addClass('grid');
		
		$('.display').html('<b><?= $text_display; ?></b> <a onclick="display(\'list\');"><?= $text_list; ?></a> <b>/</b> <?= $text_grid; ?>');
		
		$.cookie('display', 'grid');
	}
}

view = $.cookie('display');

if (view) {
	display(view);
}
//--></script> 
<?= $footer; ?>