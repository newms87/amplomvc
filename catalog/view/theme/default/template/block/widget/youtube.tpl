<div id="widgetyoutube" class="box">
	<div class="box_heading"><?= $head_title; ?></div>
	<div class="box_content">
		<? if (!empty($videos)) { ?>
			<? foreach ($videos as $video) { ?>
				<div class="youtube_video">
					<h3><?= $video['title']; ?></h3>
					<iframe width="<?= $video['width']; ?>" height="<?= $video['height']; ?>" src="<?= $video['href']; ?>" frameborder="0" allowfullscreen></iframe>
				</div>
			<? } ?>
		<? } ?>
	</div>
</div>
