<table class="form">
	<tr>
		<td>
			<?= $entry_press_items; ?>
			<a class="add_press_item"><?= $button_add_press_item; ?></a>
		</td>
		<td>
			<ul id="press_list" count="<?= count($press_items); ?>">

				<? $press_items['template_row'] = array(
					'images'      => array(),
					'author'      => '',
					'date'        => '',
					'description' => '%description%',
					'href'        => '%href%',
				); ?>

				<? $press_id = 0; ?>
				<? foreach ($press_items as $key => $press) { ?>
					<? $row = ($key === 'template_row') ? '%press_id%' : $press_id++; ?>
					<li class="press_item <?= $key; ?>" press_id="<?= $row; ?>">
						<div class="press_info">
							<label class="description"><?= $entry_press_description; ?></label>
							<input type="text" name="settings[press_items][<?= $row; ?>][description]"
							       value="<?= $press['description']; ?>"/><br/>
							<label class="author"><?= $entry_press_author; ?></label>
							<input type="text" name="settings[press_items][<?= $row; ?>][author]"
							       value="<?= $press['author']; ?>"/><br/>
							<label class="date"><?= $entry_press_date; ?></label>
							<input type="text" name="settings[press_items][<?= $row; ?>][date]"
							       value="<?= $press['date']; ?>"/><br/>
							<label class="href"><?= $entry_press_href; ?></label>
							<input type="text" name="settings[press_items][<?= $row; ?>][href]"
							       value="<?= $press['href']; ?>"/>
						</div>
						<div class="press_images">
							<a class="add_image_item"><?= $button_add_image; ?></a>

							<div class="press_image_list"
							     count="<?= !empty($press['images']) ? count($press['images']) : 0; ?>">

								<? if ($key === 'template_row') {
									$press['images']['template_row'] = '%image%';
								}?>

								<? if (!empty($press['images'])) { ?>
									<? $img_row = 0; ?>
									<? foreach ($press['images'] as $img_key => $image) { ?>
										<? $image_row = $img_key === 'template_row' ? '%image_row%' : $img_row++; ?>
										<div class="press_image <?= $img_key; ?>" image_id="<?= $image_row; ?>">
											<?= $this->builder->set_builder_template('click_image'); ?>
											<?= $this->builder->image_input("settings[press_items][$row][images][$image_row]", $image); ?>
											<br/>
											<a onclick="$(this).closest('.press_image').remove()"
											   class="delete"><?= $button_remove; ?></a>
										</div>
									<? } ?>
								<? } ?>
							</div>
						</div>
						<!--
					<span class="press_description">
						<textarea class="<?= $key === 'template_row' ? 'template' : ''; ?>" name="settings[press_items][<?= $row; ?>][description]"><?= $press['description']; ?></textarea
					</span>
					-->
						<div class='button_remove' onclick="$(this).closest('.press_item').remove()"></div>
					</li>
				<? } ?>
			</ul>
			<a class="button add_press_item"><?= $button_add_press_item; ?></a>
		</td>
	</tr>
</table>

<?= $this->builder->js('ckeditor'); ?>

<script type="text/javascript">//<!--
	var image_tmp = $('#press_list .press_image_list .template_row');
	image_tmp.find('script').remove();
	var image_template = image_tmp.html();
	image_tmp.remove();

	$('.add_image_item').click(add_image_item);

	function add_image_item() {
		image_list = $(this).siblings('.press_image_list');
		press_id = $(this).closest('.press_item').attr('press_id');

		image_row = parseInt(image_list.attr('count'));

		var template = image_template
			.replace(/%press_id%/g, press_id)
			.replace(/%image_row%/g, image_row)
			.replace(/%image%/g, '');

		$(this).siblings('.press_image_list').append($('<div class="press_image ' + image_row + '" />').append(template));

		image_list.attr('count', image_row + 1);
	}

	var list_template = $('#press_list .template_row');
	var press_template = list_template.html();
	list_template.remove();

	$('.add_press_item').click(function () {
		press_list = $('#press_list');

		press_id = parseInt(press_list.attr('count'));

		var template = press_template
			.replace(/%press_id%/g, press_id)
			.replace(/%href%/g, '')
			.replace(/%description%/g, '');

		template = $(template);

		press_list.append($('<li class="press_item" press_id="' + press_id + '" />').append(template));

		init_ckeditor_for(template.find('.ckedit'));

		template.find('.add_image_item').click(add_image_item);

		press_list.attr('count', press_id + 1);
	});
	//--></script>
