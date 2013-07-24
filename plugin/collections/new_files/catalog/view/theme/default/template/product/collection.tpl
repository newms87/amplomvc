<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content" class="collections">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>
	
	<h1><?= $heading_title; ?></h1>
	
	<div class="header-info">
		<? if (!empty($thumb)) { ?>
		<div class="image">
			<img src="<?= $thumb; ?>" alt="<?= $heading_title; ?>" />
		</div>
		<? } ?>
		
		<? if ($description) { ?>
		<div class="description"><?= $description; ?></div>
		<? } ?>
	</div>
	
	<? if (!empty($block_product_list)) { ?>
		<div class="item-filter">
			<div class="limit"><?= $limits; ?></div>
			<div class="sort"><?= $sorts; ?></div>
		</div>
	
		<?= $block_product_list; ?>
		
		<div class="pagination"><?= $pagination; ?></div>
	<? } else { ?>
		<div class="section"><?= $text_empty; ?></div>
		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
		</div>
	<? } ?>
	
	<?= $content_bottom; ?>
</div>

<?= $footer; ?>