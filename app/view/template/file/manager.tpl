<?= $is_ajax ? '' : call('header'); ?>

<? if (!$is_ajax) { ?>
<div class="amp-file-manager row">
	<div class="wrap">
		<? } ?>

		<input type="file" multiple class="amp-fm-input"/>

		<div class="amp-fm-drop">
			<div class="amp-fm-file-list">
				<div class="file" data-row="__ac_template__">
					<input type="checkbox" name="fm_file[]" class="amp-fm-select"/>

					<div class="thumbnail"></div>
					<div class="name"></div>
					<div class="controls">
						<div class="remove-file">
							<i class="fa fa-close"></i>
						</div>
					</div>

					<div class="progress-bar">
						<div class="progress-msg"></div>
						<div class="progress"></div>
					</div>
				</div>
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
