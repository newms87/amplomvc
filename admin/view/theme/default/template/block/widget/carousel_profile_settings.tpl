<? foreach ($profile_settings as $row => $profile_setting) { ?>
	<div data-extend="tab-profile-setting-<?= $row; ?>">
		<h2><?= _l("Carousel Settings"); ?></h2>
		<table class="form">
			<tr>
				<td><?= _l("Pause Time"); ?></td>
				<td><input type="text" size="2" name="profile_settings[<?= $row; ?>][params][pauseTime]" value="<?= $profile_setting['params']['pauseTime']; ?>"/></td>
			</tr>
			<tr>
				<td><?= _l("Animation Speed"); ?></td>
				<td><input type="text" size="2" name="profile_settings[<?= $row; ?>][params][animSpeed]" value="<?= $profile_setting['params']['animSpeed']; ?>"/></td>
			</tr>
		</table>

		<div class="ac_carousel_list clearfix">
			<? foreach ($profile_setting['slides'] as $slide_row => $slide) { ?>
				<div class="ac_carousel_slide" data-row="<?= $slide_row; ?>">
					<?= $this->builder->imageInput("profile_settings[$row][slides][$slide_row][image]", $slide['image']); ?>
					<div class="button delete" onclick="$(this).closest('.ac_carousel_slide').remove()">X</div>
				</div>
			<? } ?>
		</div>
		<div class="button add_carousel_slide"><?= _l("Add Slide"); ?></div>
	</div>
<? } ?>

<script type="text/javascript">
	$('.ac_carousel_slide[data-row=__ac_template__]').parent().ac_template('ac_carousel_list', {defaults: <?= json_encode($profile_setting); ?>});

	$('.add_carousel_slide').click(function () {
		console.log('adding..');
		$(this).siblings('.ac_carousel_list').ac_template('ac_carousel_list', 'add');
	});

	$('.ac_carousel_list').sortable();
</script>
