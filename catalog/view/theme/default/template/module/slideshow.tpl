<div class="slideshow">
	<div id="slideshow<?= $module; ?>" class="nivoSlider" style="width: <?= $width; ?>px; height: <?= $height; ?>px;">
		<? foreach ($banners as $banner) { ?>
			<? if ($banner['link']) { ?>
				<a href="<?= $banner['link']; ?>"><img src="<?= $banner['image']; ?>" alt="<?= $banner['title']; ?>"/></a>
			<? } else { ?>
				<img src="<?= $banner['image']; ?>" alt="<?= $banner['title']; ?>"/>
			<? } ?>
		<? } ?>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		$('#slideshow<?= $module; ?>').nivoSlider();
	});
	</script>
