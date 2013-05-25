<?= $header; ?>
<?= $column_left; ?>
<?= $column_right; ?>
<div id="content" class="collections">
	<?= $content_top; ?>
	<?= $this->builder->display_breadcrumbs(); ?>
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
	
	<? if (!empty($collections)) { ?>
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
		<? foreach ($collections as $collection) { ?>
		<div class="item_block">
			<? if ($collection['thumb']) { ?>
			<div class="image"><a href="<?= $collection['href']; ?>"><img src="<?= $collection['thumb']; ?>" title="<?= $collection['name']; ?>" alt="<?= $collection['name']; ?>" /></a></div>
			<? } ?>
			<div class="item_text">
				<div class="name"><a href="<?= $collection['href']; ?>"><?= $collection['name']; ?></a></div>
				<div class="description"><?= $collection['description']; ?></div>
		 	</div>
		 	<? if ($collection['price']) { ?>
			<div class="price">
				<? if (!$collection['special']) { ?>
				<?= $collection['price']; ?>
				<? } else { ?>
				<span class="price-old"><?= $collection['price']; ?></span>
				<span class="price-new"><?= $collection['special']; ?></span>
				<? } ?>
			</div>
			<? } ?>
			<? if ($collection['rating']) { ?>
			<div class="rating">
				<img src="<?= HTTP_THEME_IMAGE . "stars-$collection[rating].png"; ?>" alt="<?= $collection['reviews']; ?>" />
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