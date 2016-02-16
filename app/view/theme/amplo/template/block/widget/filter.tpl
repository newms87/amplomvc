<div class="amp-filter">
	<div class="fields">
		<? foreach ($fields as $f) { ?>
			<div class="field <?= 'field-' . $f['name'] . ' ' . ($f['enabled'] ? 'enabled' : 'disabled'); ?>" for="<?= $f['id']; ?>">
				<label class="label amp-filter-toggle">
					<b class="on-enabled fa fa-minus"></b>
					<b class="on-disabled fa fa-plus"></b>
					<span class="text"><?= $f['label']; ?></span>
				</label>

				<div class="amp-filter-input input <?= $f['type']; ?>">
					<? switch ($f['type']) {
						case 'number':
							?>
							<input type="text" placeholder="<?= $f['placeholder']; ?>" class="input-number" name="filter[<?= $f['name']; ?>]" value="<?= $f['value']; ?>"/>
							<? break;

						case 'range':
							?>
							<input placeholder="{{From}}" class="range range-from" type="text" name="filter[<?= $f['name']; ?>][gte]" value="<?= $f['value']['gte']; ?>"/>
							<b class="dash">-</b>
							<input placeholder="{{To}}" class="range range-to" type="text" name="filter[<?= $f['name'] ?>][lte]" value="<?= $f['value']['lte']; ?>"/>
							<? break;

						case 'date':
						case 'time':
						case 'datetime':
							?>
							<input placeholder="{{From}}" class="range date-from <?= $f['type'] . 'picker'; ?>" type="text" name="filter[<?= $f['name']; ?>][gte]" value="<?= $f['value']['gte']; ?>"/>
							<b class="dash">-</b>
							<input placeholder="{{To}}" class="range date-to <?= $f['type'] . 'picker'; ?>" type="text" name="filter[<?= $f['name'] ?>][lte]" value="<?= $f['value']['lte']; ?>"/>
							<? break;

						case 'textarea':
							?>
							<textarea placeholder="<?= $f['placeholder']; ?>"><?= $f['value']; ?></textarea>
							<? break;

						case 'select':
							?>
							<label class="select">
								<?= build(array(
										'type'   => 'select',
										'name'   => "filter[{$f['name']}]",
										'select' => $f['value'],
									) + $f['build']); ?>
							</label>
							<? break;

						case 'multiselect':
							?>
							<?= build(array(
								'type'   => 'multiselect',
								'name'   => "filter[{$f['name']}]",
								'select' => $f['value'],
								'#class' => 'amp-select',
							) + $f['build']); ?>
							<? break;

						case 'text':
						default:
							?>
							<input type="text" placeholder="<?= $f['placeholder']; ?>" class="input-text" name="filter[<?= $f['name']; ?>]" value="<?= $f['value']; ?>"/>
							<? break;
					} ?>
				</div>
			</div>
		<? } ?>
	</div>

	<a href="<?= $url; ?>" data-loading="{{Applying...}}" class="button amp-filter-apply">{{Apply Filter}}</a>

	<a class="amp-filter-reset">{{Reset}}</a>
</div>


<script type="text/javascript">
	$('.amp-filter').use_once().ampFilter(<?= json_encode($options); ?>);
</script>
