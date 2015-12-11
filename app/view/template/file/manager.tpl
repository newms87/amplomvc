<?= $is_ajax ? '' : call('header'); ?>

<? if (!$is_ajax) { ?>
<div class="amp-file-manager row">
	<div class="wrap">
		<? } ?>

		<input type="file" multiple class="amp-fm-input"/>

		<div class="amp-fm-folder-view row left">
			<div class="amp-fm-file-list">
				<label for="amp-fm-file-__ac_template__" class="amp-fm-file" data-row="__ac_template__" data-file-id="__ac_template__">
					<div class="thumbnail">
						<div class="align-middle"></div>
						<div class="thumb-img col middle">
							<?= img(theme_dir('image/image-upload.png'), array(
								'width'  => $thumb_width,
								'height' => $thumb_height,
							)); ?>
						</div>
					</div>
					<div class="name"></div>
					<div class="controls">
					</div>

					<a class="remove-file">
						<i class="fa fa-trash"></i>
					</a>

					<div class="progress-bar">
						<div class="progress-msg"></div>
						<div class="progress"></div>
					</div>
					<div class="on-selected">
						<i class="fa fa-check"></i>
					</div>
				</label>
			</div>
		</div>

		<? if (!$is_ajax) { ?>
	</div>
</div>
	<script type="text/javascript">
		$('.amp-file-manager').ampFileManager({
			accept: "<?= $accept; ?>"
		});
	</script>
<? } ?>

<?= $is_ajax ? '' : call('footer'); ?>
