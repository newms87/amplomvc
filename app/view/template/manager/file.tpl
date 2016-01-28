<?= $is_ajax ? '' : call('header'); ?>

<? if (!$is_ajax) { ?>
<div class="amp-file-manager row">
	<div class="wrap">
		<? } ?>

		<input type="file" multiple class="amp-fm-input"/>

		<div class="amp-fm-drop">
			<div class="amp-fm-loading">
				<div class="align-middle"></div>
				<?= img(theme_dir('image/spinner-lg.svg')); ?>
			</div>

			<div class="amp-fm-breadcrumb-list row left">
				<div class="amp-fm-breadcrumb col auto" data-row="__ac_template__" data-file-id=""></div>
			</div>

			<div class="amp-fm-folder-view row left">
				<div class="upload-file-icon on-empty row">
					<b class="amp-sprite si-upload-icon"></b>
				</div>

				<div class="amp-fm-file-list on-filled">
					<label for="amp-fm-file-__ac_template__" class="amp-fm-file" data-row="__ac_template__" data-file-id="__ac_template__">
						<div class="thumbnail">
							<div class="align-middle"></div>
							<?= img(theme_dir('image/image-upload.png'), array(
								'width'  => $thumb_width,
								'height' => $thumb_height,
							)); ?>
						</div>

						<div class="title" data-name="title"></div>

						<div class="controls not-uploading">
						</div>

						<a class="remove-file not-uploading">
							<i class="fa fa-trash"></i>
						</a>

						<div class="progress-bar on-uploading">
							<div class="progress-msg"></div>
							<div class="progress"></div>
						</div>

						<div class="on-selected">
							<i class="fa fa-check"></i>
						</div>
					</label>
				</div>
			</div>

			<div class="amp-fm-help row">
				<div class="text">{{<b>Click or drag</b> to add a file}}</div>
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
