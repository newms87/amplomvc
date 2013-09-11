<div id="block_widget_carousel" class="box">

	<? if (!empty($show_block_title)) { ?>
		<div class="box_heading"><?= $head_title; ?></div>
	<? } ?>

	<div class="box_content">
		<div id="<?= $slider_id; ?>" class="nivoSlider">
			<? foreach ($slides as $slide) { ?>
				<? if (!empty($slide['thumb'])) { ?>
					<img src="<?= $slide['thumb']; ?>" />
				<? } ?>
			<? } ?>
		</div>
	</div>
</div>

<script type="text/javascript">//<!--
$('#<?= $slider_id; ?>').nivoSlider(<?= $json_params; ?>);
//--></script>
