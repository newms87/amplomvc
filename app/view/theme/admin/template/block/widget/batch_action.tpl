<div class="batch-action-box">
	<span class="batch_action_title">{{Batch Action}}</span>

	<?= build(array(
		'type'  => 'select',
		'name'  => 'batch_action',
		'data'  => $actions,
		'value' => 'key',
		'label' => 'label',
	)); ?>

	<? foreach ($actions as $action) {
		if (empty($action['type'])) {
			continue;
		} ?>

		<div class="action_value" id="for-<?= $action['key']; ?>" <?= attrs($action); ?>>

			<? switch ($action['type']) {
				case 'text':
					?>
					<input type="text" name="action_value" value="<?= $action['default']; ?>"/>
					<? break;
				case 'select':
					?>
					<?= build($action['build']); ?>
					<? break;

				case 'date':
				case 'time':
				case 'datetime':
					?>
					<input type="text" class="<?= $action['type'] . 'picker'; ?>" name="action_value" value="<?= $action['default']; ?>"/>
					<? break;

				default:
					break;
			} ?>

		</div>
	<? } ?>

	<a class="button batch-action-go" data-loading="{{...}}">{{Go}}</a>

	<script type="text/javascript">
		$.ac_datepicker();

		$('select[name=batch_action]').change(function () {
			$('.action_value').removeClass('active');
			$('#for-' + $(this).val()).addClass('active');
		}).change();

		$('.batch-action-go').click(do_batch_action);

		function do_batch_action(action) {
			if (!action || typeof action != 'string') {
				action = $('select[name=batch_action]').val();
			}

			var $this = $(this);
			var $selected = $('<?= $selector; ?>:checked');

			av = $('.action_value.active [name=action_value]');

			av = av.val() || '';

			var data = $selected.serializeArray();

			data.push({name: 'action', value: action});
			data.push({name: 'value', value: av});

			$this.loading();

			$.post('<?= $url; ?>', data, function (response) {
				$this.loading('stop');
				$this.parent().show_msg(response);
				$('.refresh-listing').click();
			}, 'json');

			return false;
		}
	</script>
</div>
