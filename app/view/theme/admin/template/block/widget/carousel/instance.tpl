<table class="form">
	<tr>
		<td><?= _l("Instance Identifier"); ?></td>
		<td>
			<input type="text" class="tab_name instance_name" placeholder="(eg: my-instance-1)" name="instances[<?= $row; ?>][name]" value="<?= $instance['name']; ?>"/>
		</td>
	</tr>
	<tr>
		<td><?= _l("Instance Title"); ?></td>
		<td>
			<input type="text" name="instances[<?= $row; ?>][title]" value="<?= $instance['title']; ?>"/>
		</td>
	</tr>
	<tr>
		<td><?= _l("Show Block Title?"); ?></td>
		<td><?= $this->builder->build('radio', $data_yes_no, "instances[$row][show_title]", $instance['show_title']); ?></td>
	</tr>
</table>

<? $row_name = "instances[$row][settings]"; ?>
<? $settings = $instance['settings']; ?>
<div class="carousel-settings">
	<h2><?= _l("Carousel Settings"); ?></h2>

	<table class="form">
		<tr>
			<td><?= _l("Slider"); ?></td>
			<td class="slider_select"><?= $this->builder->build('select', $data_sliders, $row_name . "[slider]", $settings['slider']); ?></td>
		</tr>
	</table>

	<table class="form nivo slider_settings">
		<tr>
			<td><?= _l("Pause Time"); ?></td>
			<td>
				<input type="text" size="2" name="<?= $row_name; ?>[nivo][pauseTime]" value="<?= $settings['nivo']['pauseTime']; ?>"/>
			</td>
		</tr>
		<tr>
			<td><?= _l("Animation Speed"); ?></td>
			<td>
				<input type="text" size="2" name="<?= $row_name; ?>[nivo][animSpeed]" value="<?= $settings['nivo']['animSpeed']; ?>"/>
			</td>
		</tr>
	</table>

	<table class="form slidesjs slider_settings">
		<tr>
			<td><?= _l("Width"); ?></td>
			<td>
				<input type="text" size="2" name="<?= $row_name; ?>[slidesjs][width]" value="<?= $settings['slidesjs']['width']; ?>"/>
			</td>
		</tr>
		<tr>
			<td><?= _l("Height"); ?></td>
			<td>
				<input type="text" size="2" name="<?= $row_name; ?>[slidesjs][height]" value="<?= $settings['slidesjs']['height']; ?>"/>
			</td>
		</tr>
		<tr>
			<td><?= _l("Starting Slide"); ?></td>
			<td>
				<input type="text" size="2" name="<?= $row_name; ?>[slidesjs][start]" value="<?= $settings['slidesjs']['start']; ?>"/>
			</td>
		</tr>
		<tr>
			<td><?= _l("Show Navigation"); ?></td>
			<td><?= $this->builder->build('radio', $data_true_false, $row_name . "[slidesjs][navigation][active]", $settings['slidesjs']['navigation']['active']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Navigation Effect"); ?></td>
			<td><?= $this->builder->build('select', $data_effects, $row_name . "[slidesjs][navigation][effect]", $settings['slidesjs']['navigation']['effect']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Show Pagination"); ?></td>
			<td><?= $this->builder->build('radio', $data_true_false, $row_name . "[slidesjs][pagination][active]", $settings['slidesjs']['pagination']['active']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Pagination Effect"); ?></td>
			<td><?= $this->builder->build('select', $data_effects, $row_name . "[slidesjs][pagination][effect]", $settings['slidesjs']['pagination']['effect']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Show Controls"); ?></td>
			<td><?= $this->builder->build('radio', $data_true_false, $row_name . "[slidesjs][play][active]", $settings['slidesjs']['play']['active']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Controls Effect"); ?></td>
			<td><?= $this->builder->build('select', $data_effects, $row_name . "[slidesjs][play][effect]", $settings['slidesjs']['play']['effect']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Auto Play"); ?></td>
			<td><?= $this->builder->build('radio', $data_true_false, $row_name . "[slidesjs][play][auto]", $settings['slidesjs']['play']['auto']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Swap Stop / Play Buttons"); ?></td>
			<td><?= $this->builder->build('radio', $data_true_false, $row_name . "[slidesjs][play][swap]", $settings['slidesjs']['play']['swap']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Pause Slides on Hover"); ?></td>
			<td><?= $this->builder->build('radio', $data_true_false, $row_name . "[slidesjs][play][pauseOnHover]", $settings['slidesjs']['play']['pauseOnHover']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Slide Delay Time (ms)"); ?></td>
			<td>
				<input type="text" size="4" name="<?= $row_name; ?>[slidesjs][play][interval]" value="<?= $settings['slidesjs']['play']['interval']; ?>"/>
			</td>
		</tr>
		<tr>
			<td><?= _l("Restart Delay (if inactive) (ms)"); ?></td>
			<td>
				<input type="text" size="4" name="<?= $row_name; ?>[slidesjs][play][restartDelay]" value="<?= $settings['slidesjs']['play']['restartDelay']; ?>"/>
			</td>
		</tr>
		<tr>
			<td><?= _l("Fade Effect Speed"); ?></td>
			<td>
				<input type="text" size="2" name="<?= $row_name; ?>[slidesjs][effect][fade][speed]" value="<?= $settings['slidesjs']['effect']['fade']['speed']; ?>"/>
			</td>
		</tr>
		<tr>
			<td><?= _l("Fade Effect Cross-fade"); ?></td>
			<td><?= $this->builder->build('radio', $data_true_false, $row_name . "[slidesjs][effect][fade][crossfade]", $settings['slidesjs']['effect']['fade']['crossfade']); ?></td>
		</tr>
		<tr>
			<td><?= _l("Slide Effect Speed"); ?></td>
			<td>
				<input type="text" size="2" name="<?= $row_name; ?>[slidesjs][effect][slide][speed]" value="<?= $settings['slidesjs']['effect']['slide']['speed']; ?>"/>
			</td>
		</tr>
	</table>

	<div class="ac_carousel_list clearfix">
		<? foreach ($settings['slides'] as $slide_row => $slide) { ?>
			<? $slide_row_name = $row_name . "[slides][$slide_row]"; ?>

			<div class="ac_carousel_slide" data-row="<?= $slide_row; ?>">
				<input type="text" class="imageinput" name="<?= $slide_row_name . '[image]'; ?>" value="<?= $slide['image']; ?>"/>
				<input class="slide_href" placeholder="<?= _l("URL (or leave blank)"); ?>" type="text" name="<?= $slide_row_name; ?>[href]" value="<?= $slide['href']; ?>"/>
				<?= $this->builder->build('select', $data_targets, $slide_row_name . '[target]', $slide['target']); ?>
				<div class="button delete" onclick="$(this).closest('.ac_carousel_slide').remove()">X</div>
			</div>

		<? } ?>
	</div>
	<div class="button add_carousel_slide"><?= _l("Add Slide"); ?></div>
</div>

<?php if ($last) { ?>
<script type="text/javascript">
	//Carousel Settings
	$('.ac_carousel_slide[data-row=__ac_template__]').parent().ac_template('ac_carousel_list', {defaults: <?= json_encode($instance); ?>});

	$('.add_carousel_slide').click(function () {
		$(this).siblings('.ac_carousel_list').ac_template('ac_carousel_list', 'add');
		$('.imageinput').ac_imageinput();
	});

	$('.ac_carousel_list').sortable();

	$('.slider_select select').change(function () {
		var form = $(this).closest('.instance');

		form.find('.slider_settings').hide();
		form.find('.' + $(this).val()).show();
	}).change();

	$('.imageinput').ac_imageinput();
</script>
<? } ?>
