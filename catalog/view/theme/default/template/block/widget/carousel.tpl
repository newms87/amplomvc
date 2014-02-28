<div class="block_widget_carousel" class="box">

	<? if (!empty($show_block_title)) { ?>
		<div class="box_heading"><?= _l("AC Carousel"); ?></div>
	<? } ?>

	<div class="box_content">
		<div id="<?= $slider_id; ?>" <?= count($slides) > 1 ? "class=\"slider $slider\"" : ''; ?>>
			<? foreach ($slides as $slide) { ?>
				<? if (!empty($slide['thumb'])) { ?>
					<img class="slide" src="<?= $slide['thumb']; ?>"/>
				<? } ?>
			<? } ?>
		</div>
	</div>
</div>

<? if (count($slides) > 1) { ?>
	<? if ($slider === 'nivo') { ?>
		<script type="text/javascript" src="<?= HTTP_JS . 'jquery/nivo_slider/nivo-slider.js'; ?>"></script>

		<script type="text/javascript">
			$('#<?= $slider_id; ?>').nivoSlider(<?= $json_params; ?>);
		</script>
	<? } else { ?>
		<script type="text/javascript" src="<?= HTTP_JS . 'jquery/slidejs/jquery.slides.min.js'; ?>"></script>

		<script type="text/javascript">
			$('#<?= $slider_id; ?>').slidesjs(<?= $json_params; ?>);
		</script>
	<? } ?>
<? } ?>
