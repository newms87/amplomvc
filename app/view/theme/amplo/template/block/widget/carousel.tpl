<div class="block-widget-carousel" class="box">

	<? if (!empty($show_block_title)) { ?>
		<div class="heading">{{AC Carousel}}</div>
	<? } ?>

	<div class="content">
		<div id="<?= $name; ?>" <?= count($slides) > 1 ? "class=\"slider $slider\"" : ''; ?>>
			<? foreach ($slides as $slide) { ?>
				<? if (!empty($slide['href'])) { ?>
					<a class="slide" href="<?= $slide['href']; ?>" target="<?= $slide['target']; ?>">
						<? if (!empty($slide['image'])) { ?>
							<img class="image" src="<?= image($slide['image'], $slide['image_width'], $slide['image_height']); ?>"/>
						<? } ?>
					</a>

				<? } else { ?>
					<span class="slide">
						<? if (!empty($slide['image'])) { ?>
							<img class="image" src="<?= image($slide['image'], $slide['image_width'], $slide['image_height']); ?>"/>
						<? } ?>
					</span>
				<? } ?>
			<? } ?>
		</div>
	</div>
</div>

<? if (count($slides) > 1) { ?>
	<? if ($slider === 'nivo') { ?>
		<script type="text/javascript" src="<?= URL_RESOURCES . 'js/jquery/nivo_slider/nivo-slider.js'; ?>"></script>

		<script type="text/javascript">
			$('#<?= $name; ?>').nivoSlider(<?= json_encode($nivo); ?>);
		</script>
	<? } else { ?>
		<script type="text/javascript" src="<?= URL_RESOURCES . 'js/jquery/slidejs/jquery.slides.min.js'; ?>"></script>

		<script type="text/javascript">
			$('#<?= $name; ?>').slidesjs(<?= json_encode($slidesjs); ?>);
		</script>
	<? } ?>
<? } ?>
