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
		<td><?= build('radio', array(
	'name'   => "instances[$row][show_title]",
	'data'   => $data_yes_no,
	'select' => $instance['show_title']
)); ?></td>
	</tr>
</table>

<? $row_name = "instances[$row][settings]"; ?>
<? $settings = $instance['settings']; ?>
<div class="carousel-settings">
	<h2><?= _l("Carousel Settings"); ?></h2>

	<table class="form">
		<tr>
			<td><?= _l("Slider"); ?></td>
			<td class="slider_select"><?= build('select', array(
	'name'   => $row_name . "[slider]",
	'data'   => $data_sliders,
	'select' => $settings['slider']
)); ?></td>
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
			<td><?= build('radio', array(
	'name'   => $row_name . "[slidesjs][navigation][active]",
	'data'   => $data_true_false,
	'select' => $settings['slidesjs']['navigation']['active']
)); ?></td>
		</tr>
		<tr>
			<td><?= _l("Navigation Effect"); ?></td>
			<td><?= build('select', array(
	'name'   => $row_name . "[slidesjs][navigation][effect]",
	'data'   => $data_effects,
	'select' => $settings['slidesjs']['navigation']['effect']
)); ?></td>
		</tr>
		<tr>
			<td><?= _l("Show Pagination"); ?></td>
			<td><?= build('radio', array(
	'name'   => $row_name . "[slidesjs][pagination][active]",
	'data'   => $data_true_false,
	'select' => $settings['slidesjs']['pagination']['active']
)); ?></td>
		</tr>
		<tr>
			<td><?= _l("Pagination Effect"); ?></td>
			<td><?= build('select', array(
	'name'   => $row_name . "[slidesjs][pagination][effect]",
	'data'   => $data_effects,
	'select' => $settings['slidesjs']['pagination']['effect']
)); ?></td>
		</tr>
		<tr>
			<td><?= _l("Show Controls"); ?></td>
			<td><?= build('radio', array(
	'name'   => $row_name . "[slidesjs][play][active]",
	'data'   => $data_true_false,
	'select' => $settings['slidesjs']['play']['active']
)); ?></td>
		</tr>
		<tr>
			<td><?= _l("Controls Effect"); ?></td>
			<td><?= build('select', array(
	'name'   => $row_name . "[slidesjs][play][effect]",
	'data'   => $data_effects,
	'select' => $settings['slidesjs']['play']['effect']
)); ?></td>
		</tr>
		<tr>
			<td><?= _l("Auto Play"); ?></td>
			<td><?= build('radio', array(
	'name'   => $row_name . "[slidesjs][play][auto]",
	'data'   => $data_true_false,
	'select' => $settings['slidesjs']['play']['auto']
)); ?></td>
		</tr>
		<tr>
			<td><?= _l("Swap Stop / Play Buttons"); ?></td>
			<td><?= build('radio', array(
	'name'   => $row_name . "[slidesjs][play][swap]",
	'data'   => $data_true_false,
	'select' => $settings['slidesjs']['play']['swap']
)); ?></td>
		</tr>
		<tr>
			<td><?= _l("Pause Slides on Hover"); ?></td>
			<td><?= build('radio', array(
	'name'   => $row_name . "[slidesjs][play][pauseOnHover]",
	'data'   => $data_true_false,
	'select' => $settings['slidesjs']['play']['pauseOnHover']
)); ?></td>
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
			<td><?= build('radio', array(
	'name'   => $row_name . "[slidesjs][effect][fade][crossfade]",
	'data'   => $data_true_false,
	'select' => $settings['slidesjs']['effect']['fade']['crossfade']
)); ?></td>
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
				<input class="slide_href" placeholder="<?= _l("URL (or leave blank)"); ?>" type="text" name="<?= $slide_row_name; ?>[href]" value="<?= $slide['href']; ?>"/><?= build('select', array(
					'name'   => $slide_row_name . '[target]',
					'data'   => $data_targets,
					'select' => $slide['target']
				)); ?>
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
