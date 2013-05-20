<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div class="content collections">
	<?= $content_top; ?>
	<?= $this->builder->display_breadcrumbs();?>
	<h1><?= $heading_title; ?></h1>
	<? if ($thumb || $description) { ?>
	<div class="header-info">
		<? if ($thumb) { ?>
		<div class="image">
			<img src="<?= $thumb; ?>" alt="<?= $heading_title; ?>" />
		</div>
		<? } ?>
		<? if ($description) { ?>
		<div class="description"><?= $description; ?></div>
		<? } ?>
	</div>
	<? } ?>
	
	<? if (!empty($products)) { ?>
	<div class="item-filter">
		<div class="list_grid_toggle">
			<span><?= $text_display; ?></span>
			<a id="toggle_list" class="active"><?= $text_list; ?></a> <b>/</b> <a id="toggle_grid"><?= $text_grid;?></a>
		</div>
		<div class="limit">
			<span><?= $text_limit; ?></span>
			<? foreach($limits as $value => $limit_text){ ?>
			<a <?= $value == $limit ? "class='selected'" : "href=\"$limit_url$value\"";?>><?= $limit_text; ?></a>
			<? } ?>
		</div>
		<div class="sort">
			<span class="sort_text"><?= $text_sort; ?></span>
			<?= $this->builder->build('select', $sorts, 'sort_list', $sort_select);?>
		</div>
	</div>
	<div id="catalog_list" class='grid'>
		<? foreach ($products as $product) { ?>
		<div class="item_block">
			<? if ($product['thumb']) { ?>
			<div class="image"><a href="<?= $product['href']; ?>"><img src="<?= $product['thumb']; ?>" title="<?= $product['name']; ?>" alt="<?= $product['name']; ?>" /></a></div>
			<? } ?>
			<div class="item_text">
				<div class="name"><a href="<?= $product['href']; ?>"><?= $product['name']; ?></a></div>
				<div class="description"><?= $product['description']; ?></div>
		 </div>
		 <? if ($product['price']) { ?>
			<div class="price">
				<? if (!$product['special']) { ?>
				<?= $product['price']; ?>
				<? } else { ?>
				<span class="price-old"><?= $product['price']; ?></span>
				<span class="price-new"><?= $product['special']; ?></span>
				<? } ?>
			</div>
			<? } ?>
			<? if ($product['rating']) { ?>
			<div class="rating">
				<img src="<?= HTTP_THEME_IMAGE . "stars-$product[rating].png"; ?>" alt="<?= $product['reviews']; ?>" />
			</div>
	 	<? } ?>
		</div>
		<? } ?>
	</div>
	<div class="pagination"><?= $pagination; ?></div>
	<? } else { ?>
	<div class="content"><?= $text_empty; ?></div>
	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
	</div>
	<? } ?>
	<?= $content_bottom; ?>
</div>
<script type="text/javascript">
//<!--
$('[name=sort_list]').change(function(){
	location = '<?= $sort_url;?>' + '&' + $(this).val();
});

$('[name=show_limit]').change(function(){
	location = '<?= $limit_url;?>' + '&limit=' + $(this).val();
});

$('.list_grid_toggle > a').click(function(){
	if($(this).hasClass('active')) return;
	
	view = $(this).attr('id');
	
	$('.list_grid_toggle a.active').removeClass('active');
	$(this).addClass('active');
	
	if (view == 'toggle_list') {
		$('#catalog_list').removeClass('grid').addClass('list');
		
		$.cookie('display', 'list'); 
	} else {
		$('#catalog_list').removeClass('list').addClass('grid');
		
		$.cookie('display', 'grid');
	}
});

view = $.cookie('display');

if (view) {
	$('#toggle_' + view).click();
}
//--></script> 
<?= $footer; ?>