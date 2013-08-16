<div id="carousel<?= $module; ?>">
	<ul class="jcarousel-skin-opencart">
		<? foreach ($banners as $banner) { ?>
			<li><a href="<?= $banner['link']; ?>"><img src="<?= $banner['image']; ?>" alt="<?= $banner['title']; ?>"
			                                           title="<?= $banner['title']; ?>"/></a></li>
		<? } ?>
	</ul>
</div>
<script type="text/javascript">
	//<!--
	$('#carousel<?= $module; ?> ul').jcarousel({
		vertical: false,
		visible: <?= $limit; ?>,
		scroll: <?= $scroll; ?>
	});
	//--></script>