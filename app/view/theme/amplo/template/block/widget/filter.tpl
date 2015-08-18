<div class="amp-filter">
	<div class="fields">
		<? foreach ($fields as $f) { ?>
			<label class="field" for="<?= $f['id']; ?>">
				<? html_dump($f, 'filter'); ?>

				<span class="label"><?= $f['label']; ?></span>

				<div class="input">
					<? switch ($f['type']) {
						case 'number':
							?>
							<input type="text" name="filter[<?= $f['name']; ?>]" value="<?= $f['value']; ?>" placeholder="<?= $f['placeholder']; ?>"/>
							<? break;

						case 'range':
							?>
							<? break;

						case 'date':
						case 'time':
						case 'datetime':
							?>
							<? break;

						case 'textarea':
							?>
							<? break;

						case 'text':
						default:
							?>
							<input type="text" name="filter[<?= $f['name']; ?>]" value="<?= $f['value']; ?>"/>
							<? break;
					} ?>
				</div>
			</label>
		<? } ?>
	</div>

	<a href="<?= $url; ?>" data-loading="{{Applying...}}" class="button amp-apply-filter">{{Apply Filter}}</a>
</div>


<script type="text/javascript">
	$('.amp-filter').use_once().amploFilter({
		replace: '<?= $replace; ?>'
	});
</script>
