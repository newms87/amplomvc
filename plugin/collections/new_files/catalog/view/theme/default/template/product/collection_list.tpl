<?= $header; ?>
<?= $column_left; ?>
<?= $column_right; ?>
<div id="content" class="collection_list">
	<?= $content_top; ?>
	
	<?= $breadcrumbs; ?>
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
	
	<? if (!empty($block_collection_list)) { ?>
		<div class="item-filter">
			<div class="limit"><?= $limits; ?></div>
			<div class="sort"><?= $sorts; ?></div>
		</div>
	
		<?= $block_collection_list; ?>
		
		<div class="pagination"><?= $pagination; ?></div>
	<? } else { ?>
		<div class="content"><?= $text_empty; ?></div>
		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
		</div>
	<? } ?>
	
	<?= $content_bottom; ?>
</div>

<?= $footer; ?>