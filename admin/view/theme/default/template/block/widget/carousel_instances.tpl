<div id="instances_tab_list" class="vtabs">
	<div id="add_instance" class="add-vtab"><?= _l("New Instance"); ?></div>

	<? foreach ($instances as $row => $instance) { ?>
		<a href="#tab-instance-<?= $row; ?>" data-row="<?= $row; ?>">
			<span class="tab_name"><?= $instance['name']; ?></span>
			<img src="<?= URL_THEME_IMAGE . 'delete.png'; ?>" onclick="return false" class="delete_tab"/>
		</a>
	<? } ?>
</div>

<div id="instances_list">
	<? foreach ($instances as $row => $instance) { ?>
		<div id="tab-instance-<?= $row; ?>" data-row="<?= $row; ?>" class="vtabs-content instance">
			<table class="form">
				<tr>
					<td><?= _l("Instance Name"); ?></td>
					<td>
						<input type="text" class="tab_name instance_name" name="instances[<?= $row; ?>][name]" value="<?= $instance['name']; ?>"/>
					</td>
				</tr>
				<tr>
					<td><?= _l("Show Block Title?"); ?></td>
					<td><?= $this->builder->build('radio', $data_yes_no, "instances[$row][show_title]", $instance['show_title']); ?></td>
				</tr>
			</table>

			<? $row_name = "instance[$row][settings]"; ?>
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
						<td><?= $this->builder->build('radio', $data_yes_no, $row_name . "[slidesjs][navigation][active]", $settings['slidesjs']['navigation']['active']); ?></td>
					</tr>
					<tr>
						<td><?= _l("Navigation Effect"); ?></td>
						<td><?= $this->builder->build('select', $data_effects, $row_name . "[slidesjs][navigation][effect]", $settings['slidesjs']['navigation']['effect']); ?></td>
					</tr>
					<tr>
						<td><?= _l("Show Pagination"); ?></td>
						<td><?= $this->builder->build('radio', $data_yes_no, $row_name . "[slidesjs][pagination][active]", $settings['slidesjs']['pagination']['active']); ?></td>
					</tr>
					<tr>
						<td><?= _l("Pagination Effect"); ?></td>
						<td><?= $this->builder->build('select', $data_effects, $row_name . "[slidesjs][pagination][effect]", $settings['slidesjs']['pagination']['effect']); ?></td>
					</tr>
					<tr>
						<td><?= _l("Show Controls"); ?></td>
						<td><?= $this->builder->build('radio', $data_yes_no, $row_name . "[slidesjs][play][active]", $settings['slidesjs']['play']['active']); ?></td>
					</tr>
					<tr>
						<td><?= _l("Controls Effect"); ?></td>
						<td><?= $this->builder->build('select', $data_effects, $row_name . "[slidesjs][play][effect]", $settings['slidesjs']['play']['effect']); ?></td>
					</tr>
					<tr>
						<td><?= _l("Auto Play"); ?></td>
						<td><?= $this->builder->build('radio', $data_yes_no, $row_name . "[slidesjs][play][auto]", $settings['slidesjs']['play']['auto']); ?></td>
					</tr>
					<tr>
						<td><?= _l("Swap Stop / Play Buttons"); ?></td>
						<td><?= $this->builder->build('radio', $data_yes_no, $row_name . "[slidesjs][play][swap]", $settings['slidesjs']['play']['swap']); ?></td>
					</tr>
					<tr>
						<td><?= _l("Pause Slides on Hover"); ?></td>
						<td><?= $this->builder->build('radio', $data_yes_no, $row_name . "[slidesjs][play][pauseOnHover]", $settings['slidesjs']['play']['pauseOnHover']); ?></td>
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
						<td><?= $this->builder->build('radio', $data_yes_no, $row_name . "[slidesjs][effect][fade][crossfade]", $settings['slidesjs']['effect']['fade']['crossfade']); ?></td>
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
		</div>
	<? } ?>

</div>


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


	//Update Tab Name
	$('.instance_name').keyup(function () {
		update_instance_select();
	});

	//Delete Tab
	$('#instances_tab_list .delete_tab').click(function () {
		if ($(this).closest('a').hasClass('selected')) {
			$(this).closest('.vtabs').children('a:first').click();
		}

		var tab = $(this).closest('a');

		$(tab.attr('href')).remove();
		tab.remove();

		update_instance_select();

		return false;
	});

	$('#instances_tab_list').ac_template('instances_tab_list');
	$('#instances_list').ac_template('instances_list', {defaults: <?= json_encode($instances['__ac_template__']); ?>});

	$('#add_instance').click(function () {
		var pstab = $.ac_template('instances_tab_list', 'add');
		$.ac_template('instances_list', 'add');

		pstab.closest('.vtabs').children('a').tabs();
		pstab.click();

		update_instance_select();
	});

	function update_instance_select() {
		var profile_template = $.ac_template.templates['profile_list'].template.find('select[name*=instance_id]');
		context = $('select[name*=instance_id]').add(profile_template);

		var options = '';

		$('#instances_tab_list a').each(function (i, e) {
			options += '<option value="' + $(e).attr('data-row') + '">' + $(e).find('.tab_name').html() + '</option>';
		});

		context.html(options);
	}

	//Tabs
	$('#instances_tab_list a').tabs();
</script>
