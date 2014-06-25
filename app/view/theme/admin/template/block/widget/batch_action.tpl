<span class="batch_action_title"><?= _l("Batch Action"); ?></span>

<?= build('select', array(
	'name'  => 'batch_action',
	'data'  => $actions,
	'key'   => 'key',
	'value' => 'label',
)); ?>

<? $ckeditor = false; ?>

<? foreach ($actions as $action) {
	if (empty($action['type'])) {
		continue;
	} ?>

	<div class="action_value" id="for-<?= $action['key']; ?>" <?= $action['attrs']; ?>>

		<? switch ($action['type']) {
			case 'text':
				?>
				<input type="text" name="action_value" value="<?= $action['default']; ?>"/>
				<? break;
			case 'ckedit':
				?>
				<? $ckeditor = true; ?>
				<textarea class="ckedit batch_ckedit" id="ba-<?= $action['key']; ?>" name="action_value"><?= $action['default']; ?></textarea>
				<? break;
			case 'select':
				?>
				<?= build('select', array(
					'name'   => "action_value",
					'data'   => $action['build_data'],
					'select' => $action['default'],
					'key'    => $action['build_config'][0],
					'value'  => $action['build_config'][1],
				)); ?>
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

<a class="button batch-action-go" data-loading="<?= _l("..."); ?>"><?= _l("Go"); ?></a>

<? if ($ckeditor) {
	echo build_js('ckeditor');
} ?>

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

		if (!$selected.length) {
			alert("<?= _l("Please select items to perform the batch action on."); ?>");
			return false;
		}

		av = $('.action_value.active [name=action_value]');

		if (av.hasClass('ckedit'))
			av = CKEDITOR.instances[av.attr('id')].getData();
		else
			av = av.val() || '';

		var data = $selected.serializeArray();

		data.push({name: 'action', value: action});
		data.push({name: 'value', value: av});

		$this.loading();

		$.post('<?= $url; ?>', data, function (response) {
			$this.loading('stop');
			$this.ac_msg(response);
			$('.refresh-listing').click();
		}, 'json');

		return false;
	}
</script>
