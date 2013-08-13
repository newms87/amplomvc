<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content" class="collection_list">
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>
	
	<h1><?= $head_title; ?></h1>
	
	<div class="header-info">
		<? if (!empty($thumb)) { ?>
		<div class="image">
			<img src="<?= $thumb; ?>" alt="<?= $head_title; ?>" />
		</div>
		<? } ?>
		
		<? if (!empty($description)) { ?>
		<div class="description"><?= $description; ?></div>
		<? } ?>
	</div>
	
	<? if (!empty($block_collection_list)) { ?>
		<div class="item-filter">
			<div class="limit"><?= $limits; ?></div>
			<div class="sort"><?= $sorts; ?></div>
		</div>
	
		<?= $block_collection_list; ?>
		
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