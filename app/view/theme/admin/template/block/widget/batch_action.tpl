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

<a class="button" onclick="do_batch_action()"><?= _l("Go"); ?></a>

<? if ($ckeditor) {
	echo build_js('ckeditor');
} ?>

<script type="text/javascript">
	$.ac_datepicker();

	$('select[name=batch_action]').change(function () {
		$('.action_value').removeClass('active');
		$('#for-' + $(this).val()).addClass('active');
	}).change();

	function do_batch_action(action) {
		var $listing = $('<?= $replace; ?>');
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

		data.push({name: 'action', value: action || $('select[name=batch_action]').val()});
		data.push({name: 'value', value: av});

		$listing.addClass('loading');
		var url = '<?= $url; ?>';

		url += (url.match(/\?/) ? '&' : '?') + window.location.search;

		$.post(url, data, function (response) {
			$listing.replaceWith(response);
		});

		return false;
	}
</script>
