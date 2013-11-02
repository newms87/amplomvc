<div id="banner<?= $module; ?>" class="banner">
	<? foreach ($banners as $banner) { ?>
		<? if ($banner['link']) { ?>
			<div><a href="<?= $banner['link']; ?>"><img src="<?= $banner['image']; ?>" alt="<?= $banner['title']; ?>"
			                                            title="<?= $banner['title']; ?>"/></a></div>
		<? } else { ?>
			<div><img src="<?= $banner['image']; ?>" alt="<?= $banner['title']; ?>" title="<?= $banner['title']; ?>"/>
			</div>
		<? } ?>
	<? } ?>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		$('#banner<?= $module; ?> div:first-child').css('display', 'block');
	});

	var banner = function () {
		$('#banner<?= $module; ?>').cycle({
			before: function (current, next) {
				$(next).parent().height($(next).outerHeight());
			}
		});
	}

	setTimeout(banner, 2000);
</script>
