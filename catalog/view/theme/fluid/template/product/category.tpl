<?= _call('common/header'); ?>
<?= _area('left'); ?><?= _area('right'); ?>
<div class="content category">
	<?= _breadcrumbs(); ?>
	<?= _area('top'); ?>

	<h1><?= $page_title; ?></h1>

	<div class="category_info">
		<? if (!empty($thumb)) { ?>
			<div class="image">
				<img src="<?= $thumb; ?>" alt="<?= $head_title; ?>"/>
			</div>
		<? } ?>

		<? if (!empty($description)) { ?>
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
		<div class="section"><?= _l("There are no products under %s right now, but you can find more products under one of our other categories!", $category_name); ?></div>
		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= _l("Continue"); ?></a></div>
		</div>
	<? } ?>

	<?= _area('bottom'); ?>
</div>

<?= _call('common/footer'); ?>