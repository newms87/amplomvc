<div id='press_entries' class="box">
	<h1><?= $head_title; ?></h1>

	<div class="box_content">
		<ul id='press_list'>
			<? foreach ($press_list as $row => $press) { ?>
				<li>
					<div class="press_item">
						<? if (!empty($press['images'])) { ?>
							<? $colorbox_group = 'press-item-' . $row; ?>
							<div class="press_images">
								<img src="<?= $press['thumb']; ?>" onclick="$('.<?= $colorbox_group; ?>:first').click()"/>

								<? foreach ($press['images'] as $image) { ?>
									<a href="<?= $image; ?>" class="<?= $colorbox_group; ?>" title="<?= $press['description']; ?>" ></a>
								<? } ?>
							</div>
						<? } ?>
						<? if (false && $press['href']) { ?><a class="press_link" href="<?= $press['href']; ?>"><?= $press['description']; ?></a><? } ?>
					</div>
				</li>
			<? } ?>
		</ul>
	</div>
</div>

<script type="text/javascript">
<? foreach ($press_list as $row => $press) { ?>
	<? $colorbox_group = 'press-item-' . $row; ?>
	$('.<?= $colorbox_group; ?>').colorbox({
		rel: '<?= $colorbox_group; ?>',
		width:'80%',
		height: '80%'
	});
<? } ?>
</script>
