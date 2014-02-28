<? foreach ($profile_settings as $row => $profile_setting) { ?>
	<div class="profile_setting" data-extend="tab-profile-setting-<?= $row; ?>">
		<h2><?= _l("Carousel Settings"); ?></h2>

		<table class="form">
			<tr>
				<td><?= _l("Slider"); ?></td>
				<td class="slider_select"><?= $this->builder->build('select', $data_sliders, "profile_settings[$row][slider]", $profile_setting['slider']); ?></td>
			</tr>
		</table>

		<table class="form nivo slider_settings">
			<tr>
				<td><?= _l("Pause Time"); ?></td>
				<td><input type="text" size="2" name="profile_settings[<?= $row; ?>][nivo][pauseTime]" value="<?= $profile_setting['nivo']['pauseTime']; ?>"/></td>
			</tr>
			<tr>
				<td><?= _l("Animation Speed"); ?></td>
				<td><input type="text" size="2" name="profile_settings[<?= $row; ?>][nivo][animSpeed]" value="<?= $profile_setting['nivo']['animSpeed']; ?>"/></td>
			</tr>
		</table>

		<table class="form slidesjs slider_settings">
			<tr>
				<td><?= _l("Width"); ?></td>
				<td><input type="text" size="2" name="profile_settings[<?= $row; ?>][slidesjs][width]" value="<?= $profile_setting['slidesjs']['width']; ?>" /></td>
			</tr>
			<tr>
				<td><?= _l("Height"); ?></td>
				<td><input type="text" size="2" name="profile_settings[<?= $row; ?>][slidesjs][height]" value="<?= $profile_setting['slidesjs']['height']; ?>" /></td>
			</tr>
			<tr>
				<td><?= _l("Starting Slide"); ?></td>
				<td><input type="text" size="2" name="profile_settings[<?= $row; ?>][slidesjs][start]" value="<?= $profile_setting['slidesjs']['start']; ?>" /></td>
			</tr>
			<tr>
				<td><?= _l("Show Navigation"); ?></td>
				<td><?= $this->builder->build('radio', $data_yes_no, "profile_settings[$row][slidesjs][navigation][active]", $profile_setting['slidesjs']['navigation']['active']); ?></td>
			</tr>
			<tr>
				<td><?= _l("Navigation Effect"); ?></td>
				<td><?= $this->builder->build('select', $data_effects, "profile_settings[$row][slidesjs][navigation][effect]", $profile_setting['slidesjs']['navigation']['effect']); ?></td>
			</tr>
			<tr>
				<td><?= _l("Show Pagination"); ?></td>
				<td><?= $this->builder->build('radio', $data_yes_no, "profile_settings[$row][slidesjs][pagination][active]", $profile_setting['slidesjs']['pagination']['active']); ?></td>
			</tr>
			<tr>
				<td><?= _l("Pagination Effect"); ?></td>
				<td><?= $this->builder->build('select', $data_effects, "profile_settings[$row][slidesjs][pagination][effect]", $profile_setting['slidesjs']['pagination']['effect']); ?></td>
			</tr>
			<tr>
				<td><?= _l("Show Controls"); ?></td>
				<td><?= $this->builder->build('radio', $data_yes_no, "profile_settings[$row][slidesjs][play][active]", $profile_setting['slidesjs']['play']['active']); ?></td>
			</tr>
			<tr>
				<td><?= _l("Controls Effect"); ?></td>
				<td><?= $this->builder->build('select', $data_effects, "profile_settings[$row][slidesjs][play][effect]", $profile_setting['slidesjs']['play']['effect']); ?></td>
			</tr>
			<tr>
				<td><?= _l("Auto Play"); ?></td>
				<td><?= $this->builder->build('radio', $data_yes_no, "profile_settings[$row][slidesjs][play][auto]", $profile_setting['slidesjs']['play']['auto']); ?></td>
			</tr>
			<tr>
				<td><?= _l("Swap Stop / Play Buttons"); ?></td>
				<td><?= $this->builder->build('radio', $data_yes_no, "profile_settings[$row][slidesjs][play][swap]", $profile_setting['slidesjs']['play']['swap']); ?></td>
			</tr>
			<tr>
				<td><?= _l("Pause Slides on Hover"); ?></td>
				<td><?= $this->builder->build('radio', $data_yes_no, "profile_settings[$row][slidesjs][play][pauseOnHover]", $profile_setting['slidesjs']['play']['pauseOnHover']); ?></td>
			</tr>
			<tr>
				<td><?= _l("Slide Delay Time (ms)"); ?></td>
				<td><input type="text" size="4" name="profile_settings[<?= $row; ?>][slidesjs][play][interval]" value="<?= $profile_setting['slidesjs']['play']['interval']; ?>" /></td>
			</tr>
			<tr>
				<td><?= _l("Restart Delay (if inactive) (ms)"); ?></td>
				<td><input type="text" size="4" name="profile_settings[<?= $row; ?>][slidesjs][play][restartDelay]" value="<?= $profile_setting['slidesjs']['play']['restartDelay']; ?>" /></td>
			</tr>
			<tr>
				<td><?= _l("Fade Effect Speed"); ?></td>
				<td><input type="text" size="2" name="profile_settings[<?= $row; ?>][slidesjs][effect][fade][speed]" value="<?= $profile_setting['slidesjs']['effect']['fade']['speed']; ?>" /></td>
			</tr>
			<tr>
				<td><?= _l("Fade Effect Cross-fade"); ?></td>
				<td><?= $this->builder->build('radio', $data_yes_no, "profile_settings[$row][slidesjs][effect][fade][crossfade]", $profile_setting['slidesjs']['effect']['fade']['crossfade']); ?></td>
			</tr>
			<tr>
				<td><?= _l("Slide Effect Speed"); ?></td>
				<td><input type="text" size="2" name="profile_settings[<?= $row; ?>][slidesjs][effect][slide][speed]" value="<?= $profile_setting['slidesjs']['effect']['slide']['speed']; ?>" /></td>
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
		$(this).siblings('.ac_carousel_list').ac_template('ac_carousel_list', 'add');
	});

	$('.ac_carousel_list').sortable();

	$('.slider_select select').change(function(){
		var form = $(this).closest('.profile_setting');

		form.find('.slider_settings').hide();
		form.find('.' + $(this).val()).show();
	}).change();
</script>
