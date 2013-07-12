<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'banner.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_name; ?></td>
						<td><input type="text" name="name" value="<?= $name; ?>" size="100" /></td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><?= $this->builder->build('select',$statuses,'status',(int)$status); ?></td>
					</tr>
				</table>
				<div style="padding:6px 0;">
					<a style="float:right;margin-right:10px;" onclick="image_manager();" class="button">File Manager</a>
					<div style="clear:both;"></div>
				</div>
				<table id="images" class="list">
					<thead>
						<tr>
							<td class="left"><?= $entry_title; ?></td>
							<td class="left"><?= $entry_link; ?></td>
							<td class="left"><?= $entry_image; ?></td>
							<td class="right"><?= $entry_sort_order; ?></td>
							<td></td>
						</tr>
					</thead>
					<tbody>
					<? $image_row = 0; ?>
					<? foreach ($banner_images as $banner_image) { ?>
						<tr class="imagerow" id="image-row<?= $image_row; ?>">
							<td class="left"><? foreach ($languages as $language) { ?>
							<input type="text" name="banner_image[<?= $image_row; ?>][banner_image_description][<?= $language['language_id']; ?>][title]" value="<?= isset($banner_image['banner_image_description'][$language['language_id']]) ? $banner_image['banner_image_description'][$language['language_id']]['title'] : ''; ?>" />
							<img src="<?= HTTP_THEME_IMAGE . "flags/$language[image]"; ?>" title="<?= $language['name']; ?>" /><br />
							<? } ?>
							</td>
							<td class="left">
								<input type="text" name="banner_image[<?= $image_row; ?>][link]" value="<?= $banner_image['link']; ?>" size="50" />
							</td>
							<td class="left">
								<?= $this->builder->image_input("banner_image[$image_row][image]", $banner_image['image']); ?>
							</td>
							<td class="right"><input class="sortOrder" type="text" name="banner_image[<?= $image_row; ?>][sort_order]" value="<?= $banner_image['sort_order']; ?>" size="2" /></td>
							<td class="left"><a onclick="$('#image-row<?= $image_row; ?>').remove();" class="button"><?= $button_remove; ?></a></td>
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

<script type="text/javascript">//<!--


$('input[name=primary_product_image]').live("change", function() { $('input[name=image]').val($(this).val()); });

$('#images').bind('sortupdate', function(event, ui) {
	var index = 0;
	$('#images tbody tr').each(function() {
			index += 1;
			var so = $(this).find('.sortOrder');
			so.val(index);
	});
});
	
$(document).ready(function() {
	var c = {};
	$('#images').sortable({	items: 'tr.imagerow',
																	forcePlaceholderSize:true,
																	cursor: "move",
																	helper: function(event) { return $('<div class="drag-row"><table></table></div>').find('table').append($(event.target).closest('tr').clone()).end(); },
																	forceHelperSize: true,
																	forcePlaceholderSize: true,
																	scroll: true,
																	scrollSensitivity: 30,
																	scrollSpeed: 30});
});
//--></script>

<?= $this->builder->js('errors',$errors); ?>

<?= $footer; ?>