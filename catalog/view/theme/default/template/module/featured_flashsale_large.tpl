<div class="featured_flashsale">
	<img class='fs_bg_image' src='<?= $fs_bg_image; ?>' />
	<? $c = 0;?>
	<? foreach($blocks as $block){ $hide = $c > 2?"style='display:none'":'';?>
		<a href='<?= $block['href']; ?>'>
		<div class='fs_item item_<?= $c; ?>' <?= $hide; ?>>
			<img class='fs_tac' src='<?= $fs_tac; ?>' />
			<img class='fs_image' src='<?= $block['image']; ?>' />
			<? if(isset($block['name'])){?>
			<div class='fs_info'>
				<div class='fs_title'><?= $block['name']; ?></div>
				<div class='fs_teaser'><?= $block['teaser']; ?></div>
				<? if(isset($block['flashsale_id'])){?><div class='fs_countdown'><div class='flash_countdown' id='ffslarge-<?= $c; ?>' flashid='<?= $block['flashsale_id']; ?>'></div></div><? }?>
			</div>
			<? }?>
		</div>
		</a>
	<? $c++;} ?>
</div>
