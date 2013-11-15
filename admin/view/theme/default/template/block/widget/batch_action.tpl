<span class="batch_action_title"><?= $text_batch_action; ?></span>

<? $this->builder->setConfig('key', 'label'); ?>
<?= $this->builder->build('select', $actions, 'batch_action'); ?>

<? $ckeditor = false; ?>

<? foreach ($actions as $action) {
	if (empty($action['type'])) {
		continue;
	} ?>

	<div class="action_value" id="for-<?= $action['key']; ?>" <?= $action['attrs']; ?>>

		<? switch ($action['type']) {
			case 'text':
				?>
				<input type="text" name="action_value value="<?= $action['default']; ?>" />
			<? break;
			case 'ckedit':
				?>
				<? $ckeditor = true; ?>
				<textarea class="ckedit batch_ckedit" id="ba-<?= $action['key']; ?>" name="action_value"><?= $action['default']; ?></textarea>
				<? break;
			case 'select':
				?>
				<? $this->builder->setConfig($action['build_config'][0], $action['build_config'][1]); ?>
				<?= $this->builder->build('select', $action['build_data'], "action_value", $action['default']); ?>
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

<a class="button" onclick="do_batch_action()"><?= $button_batch_update; ?></a>

<? if ($ckeditor) {
	echo $this->builder->js('ckeditor');
} ?>

<script type="text/javascript">//<!--
	$.ac_datepicker();

	$('select[name=batch_action]').change(function () {
		$('.action_value').removeClass('active');
		$('#for-' + $(this).val()).addClass('active');
	});

	function do_batch_action(action) {
		selected = $('<?= $selector; ?>:checked');

		if (!selected.length) {
			alert("<?= $error_batch_action_selection; ?>");
			return false;
		}

		if (!action) {
			action = $('select[name=batch_action]').val();
		}

		av = $('.action_value.active [name=action_value]');

		if (av.hasClass('ckedit'))
			av = escape(CKEDITOR.instances[av.attr('id')].getData());
		else
			av = av.val() || '';

		url = '<?= $url; ?>'.replace(/_action_/, action).replace(/_action_value_/, av);

		location = url + '&' + selected.serialize();
	}
</script>
