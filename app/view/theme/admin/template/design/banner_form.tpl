<?= call('admin/common/header'); ?>
<div class="section">
	<?= breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/banner.png'); ?>" alt=""/> <?= _l("Banners"); ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
					href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Banner Name:"); ?></td>
						<td><input type="text" name="name" value="<?= $name; ?>" size="100"/></td>
					</tr>
					<tr>
						<td><?= _l("Status:"); ?></td>
						<td><?= $this->builder->build('select', $data_statuses, 'status', (int)$status); ?></td>
					</tr>
				</table>
				<div style="padding:6px 0;">
					<a style="float:right;margin-right:10px;" onclick="image_manager();" class="button">File Manager</a>

					<div style="clear:both;"></div>
				</div>
				<table id="images" class="list">
					<thead>
						<tr>
							<td class="left"><?= _l("Title:"); ?></td>
							<td class="left"><?= _l("Link:"); ?></td>
							<td class="left"><?= _l("Image:"); ?></td>
							<td class="right"><?= _l("Sort Order:"); ?></td>
							<td></td>
						</tr>
					</thead>
					<tbody>
						<? $image_row = 0; ?>
						<? foreach ($banner_images as $banner_image) { ?>
							<tr class="imagerow" id="image-row<?= $image_row; ?>">
								<td class="left"><? foreach ($languages as $language) { ?>
										<input type="text" name="banner_image[<?= $image_row; ?>][banner_image_description][<?= $language['language_id']; ?>][title]" value="<?= isset($banner_image['banner_image_description'][$language['language_id']]) ? $banner_image['banner_image_description'][$language['language_id']]['title'] : ''; ?>"/>
										<img src="<?= URL_THEME_IMAGE . "flags/$language[image]"; ?>"
											title="<?= $language['name']; ?>"/><br/>
									<? } ?>
								</td>
								<td class="left">
									<input type="text" name="banner_image[<?= $image_row; ?>][link]" value="<?= $banner_image['link']; ?>" size="50"/>
								</td>
								<td class="left">
									<input type="text" class="imageinput" name="banner_image[<?= $image_row; ?>][image]" value="<?= $banner_image['image']; ?>" />
								</td>
								<td class="right"><input class="sortOrder" type="text" name="banner_image[<?= $image_row; ?>][sort_order]" value="<?= $banner_image['sort_order']; ?>" size="2"/></td>
								<td class="left"><a onclick="$('#image-row<?= $image_row; ?>').remove();"
										class="button"><?= _l("Remove"); ?></a></td>
							</tr>
							<? $image_row++; ?>
						<? } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="3"></td>
							<td class="left"><a onclick="image_manager();" class="button">File Manager</a></td>
						</tr>
					</tfoot>
				</table>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">


	$('input[name=primary_product_image]').live("change", function () {
		$('input[name=image]').val($(this).val());
	});

	$('#images').bind('sortupdate', function (event, ui) {
		var index = 0;
		$('#images tbody tr').each(function () {
			index += 1;
			var so = $(this).find('.sortOrder');
			so.val(index);
		});
	});

	$(document).ready(function () {
		var c = {};
		$('#images').sortable({   items: 'tr.imagerow',
			forcePlaceholderSize: true,
			cursor: "move",
			helper: function (event) {
				return $('<div class="drag-row"><table></table></div>').find('table').append($(event.target).closest('tr').clone()).end();
			},
			forceHelperSize: true,
			forcePlaceholderSize: true,
			scroll: true,
			scrollSensitivity: 30,
			scrollSpeed: 30});
	});

	$('.imageinput').ac_imageinput();
</script>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= call('admin/common/footer'); ?>
