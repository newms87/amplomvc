<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div id="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<?= $content_top; ?>
	<? if(empty($flashsales)){?>
			<h1><?= $no_sales_heading; ?></h1>
			<div class="content"><?= $no_sales_text; ?></div>
		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
		</div>
	<? }else{ ?>
			<h1><?= $flashsale_heading; ?></h1>
			<? foreach($flashsales as $fs){?>
				<a	href='<?= $fs['href']; ?>' style='display:block;text-decoration:none!important'>
				<div class='flashsale_item'>
						<img src='<?= $polaroids[0]; ?>' class='polaroid_front' />
						<div class='fs_item'>
							<img class='fs_image' src='<?= $fs['image']; ?>' />
							<div class='fs_info'>
									<div class='fs_title'><?= $fs['name']; ?></div>
									<div class='fs_blurb'><?= $fs['teaser']; ?></div>
									<div class='fs_countdown'><div id='flashsale-sale-<?= $fs['flashsale_id']; ?>' class='flash_countdown' flashid='<?= $fs['flashsale_id']; ?>'></div></div>
							</div>
						</div>
						<img class='fs_tac' src='<?= $fs_tac; ?>' />
				</div>
				</a>
			<? } ?>
	<? } ?>
	<?= $content_bottom; ?>
</div>
<?= $footer; ?>