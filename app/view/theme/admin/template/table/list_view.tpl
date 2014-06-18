<? /*
 * Name: list_view
 *
 * This table uses the following variables:
 *
 * $columns => array(
 * 	'type' 			=> string (required) - Input and display type. Can be 'text', 'select', 'multiselect', 'image', 'int', 'date_range', 'time_range', 'datetime_range', 'format'.
 * 	'display_name' => string (required) - Display Name for the header of the column.
 * 	'align' 			=> string (optional) - 'center' (default), 'left', 'right'.
 * 	'sortable'		=> bool (optional) - Can this column can be sorted? Default is false.
 * 	'filter'			=> bool (optional) - Can this column be filtered? Default is false.
 * 	'filter_value' => mixed (optional) - Use this to override the filter value. Value is set if user has specified, otherwise the default filter value.
 * 	'build_data'	=> mixed (optional) - Fill data for the column. Used to display a key value as text, or with a filter depending on the 'type'.
 * 	'build_config' => array (optional) - Use this with 'build_data' to specify the array( $key => $value ) for the builder tool
 * );
 */
?>

<div class="table-list-view-box">
	<table class="list table-list-view">
		<thead>
			<tr>
				<? if (!empty($row_id)) { ?>
					<td width="1" class="center">
						<input type="checkbox" onclick="$('[name=\'batch[]\']').prop('checked', this.checked).change();"/>
						<a href="<?= $sort_url; ?>&sort=<?= $row_id; ?>&order=<?= ($sort === $row_id && $order === 'ASC') ? 'DESC' : 'ASC'; ?>" class="sortable <?= $row_id . ' ' . ($sort === $row_id ? strtolower($order) : ''); ?>"><?= $row_id; ?></a>
					</td>
				<? } ?>
				<td class="center column_title">
					<span><?= _l("Action"); ?></span>
				</td>
				<? foreach ($columns as $slug => $column) { ?>
					<td class="column_title <?= $column['align'] . ' ' . $slug; ?>">
						<? if ($column['sortable']) {
							$c_order = ($sort === $column['sort_value'] && $order === 'ASC') ? 'DESC' : 'ASC';
							$class   = $sort === $column['sort_value'] ? strtolower($order) : '';
							?>
							<a href="<?= $sort_url; ?>&sort=<?= $column['sort_value']; ?>&order=<?= $c_order; ?>"
							   class="sortable <?= $class; ?>"><?= $column['display_name']; ?></a>
						<? } else { ?>
							<span><?= $column['display_name']; ?></span>
						<? } ?>
					</td>
				<? } ?>
				<td class="center column_title">
					<span><?= _l("Action"); ?></span>
				</td>
			</tr>
		</thead>
		<tbody>
			<tr class="filter-list">
				<? if (!empty($row_id)) { ?>
					<td></td>
				<? } ?>
				<td align="center">
					<a class="button filter-button"><?= _l("Filter"); ?></a>
					<? if (!empty($_GET['filter'])) { ?>
						<a class="reset reset-button"><?= _l("Reset"); ?></a>
					<? } ?>
				</td>
				<? foreach ($columns as $slug => $column) { ?>
					<? if ($column['filter']) { ?>
						<td class="column_filter <?= $column['align'] . ' ' . $slug; ?>">
							<? switch ($column['filter']) {
								case 'text':
									?>
									<input type="text" name="filter[<?= $slug; ?>]" value="<?= $column['filter_value']; ?>"/>
									<? break;

								case 'int':
									?>
									<? if (!isset($column['filter_value']['low'])) {
									$column['filter_value']['low'] = null;
								}
									if (!isset($column['filter_value']['high'])) {
										$column['filter_value']['high'] = null;
									}
									?>
									<div class="zoom_hover int">
										<div class="input">
											<input type="text" class="int_low" name="filter[<?= $slug; ?>][low]" value="<?= $column['filter_value']['low']; ?>"/>
											<input type="text" class="int_high" name="filter[<?= $slug; ?>][high]" value="<?= $column['filter_value']['high']; ?>"/>
											<span class="clear">clear</span>
										</div>
										<div class="value">
											<? if (!is_null($column['filter_value']['low']) || !is_null($column['filter_value']['high'])) { ?>
												<?= $column['filter_value']['low'] . ' - ' . $column['filter_value']['high']; ?>
											<? } else { ?>
												<?= _l("Modify"); ?>
											<? } ?>
										</div>
									</div>
									<? break;
								case 'select':
									if (isset($column['build_config'])) {
										$build_key   = $column['build_config'][0];
										$build_value = $column['build_config'][1];
									} else {
										$build_key = $build_value = null;
									}

									if ($column['filter_blank']) {
										$column['build_data'] = array('' => '') + $column['build_data'];
									}

									echo build('select', array(
										'name'   => "filter[$slug]",
										'data'   => $column['build_data'],
										'select' => $column['filter_value'],
										'key'    => $build_key,
										'value'  => $build_value,
									));
									break;

								case 'multiselect':
									if (isset($column['build_config'])) {
										$build_key   = $column['build_config'][0];
										$build_value = $column['build_config'][1];
									} else {
										$build_key = $build_value = null;
									}

									?>
									<div class="zoom_hover multiselect">
										<div class="input">
											<?=
											build('multiselect', array(
												'name'   => "filter[$slug]",
												'data'   => $column['build_data'],
												'select' => $column['filter_value'],
												'key'    => $build_key,
												'value'  => $build_value,
											)); ?>
										</div>
										<div class="value">
											<? if (!empty($column['filter_value'])) {
												$vals = array();
												foreach ($column['filter_value'] as $v) {
													if ($build_key === false) {
														$vals[] = $build_value ? $column['build_data'][$v][$build_value] : $column['build_data'][$v];
													} else {
														foreach ($column['build_data'] as $bd) {
															if ($bd[$build_key] === $v) {
																$vals[] = isset($bd[$build_value]) ? $bd[$build_value] : $bd[$build_key];
															}
														}
													}
												} ?>

												<?= charlimit(implode(', ', $vals), 20, '...', false); ?>
											<? } else { ?>
												<?= _l("Modify"); ?>
											<? } ?>
										</div>
									</div>
									<? break;

								case 'date':
								case 'time':
								case 'datetime':
									?>
									<? if (!isset($column['filter_value']['start'])) {
									$column['filter_value']['start'] = null;
								}
									if (!isset($column['filter_value']['end'])) {
										$column['filter_value']['end'] = null;
									}
									?>

									<div class="zoom_hover daterange">
										<div class="input">
											<input class="date_start <?= $column['type'] . 'picker'; ?>" type="text" name="filter[<?= $slug; ?>][start]" value="<?= $column['filter_value']['start']; ?>"/>
											<input class="date_end <?= $column['type'] . 'picker'; ?>" type="text" name="filter[<?= $slug ?>][end]" value="<?= $column['filter_value']['end']; ?>"/>
											<span class="clear">clear</span>
										</div>
										<div class="value">
											<? if (!is_null($column['filter_value']['start']) || !is_null($column['filter_value']['end'])) { ?>
												<?= $column['filter_value']['start'] . ' - ' . $column['filter_value']['end']; ?>
											<? } else { ?>
												<?= _l("Modify"); ?>
											<? } ?>
										</div>
									</div>

									<? break;

								default:
									break;
							} ?>
						</td>
					<? } else { ?>
						<td></td>
					<? } ?>
				<? } ?>
				<td align="center">
					<a class="button filter-button"><?= _l("Filter"); ?></a>
					<? if (!empty($_GET['filter'])) { ?>
						<a class="reset reset-button"><?= _l("Reset"); ?></a>
					<? } ?>
				</td>
			</tr>
			<? if (!empty($rows)) { ?>
				<? foreach ($rows as $row) { ?>
					<tr class="filter-list-item" data-row-id="<?= !empty($row[$row_id]) ? $row[$row_id] : ''; ?>">
						<? if (!empty($row_id)) { ?>
							<? $uniqid = uniqid($row[$row_id]); ?>
							<td class="center">
								<input id="rowid<?= $uniqid; ?>" type="checkbox" name="batch[]" onclick="$(this).data('clicked',true)" value="<?= $row[$row_id]; ?>" <?= !empty($row['selected']) ? 'checked' : ''; ?> />
								<label for="rowid<?= $uniqid; ?>" class="rowid"><?= $row[$row_id]; ?></label>
							</td>
						<? } ?>

						<? $quick_actions = '';

						if (!empty($row['actions'])) {
							foreach ($row['actions'] as $key => $action) {
								$action['#class'] = (isset($action['#class']) ? $action['#class'] . ' ' : '') . 'action action-' . $key;
								if (!empty($action['href'])) {
									$quick_actions .= "<a href=\"$action[href]\"" . attrs($action) . ">$action[text]</a>";
								} else {
									$quick_actions .= "<span " . attrs($action) . ">$action[text]</span>";

								}
							}
						}
						?>

						<td class="center actions">
							<?= $quick_actions; ?>
						</td>
						<? foreach ($columns as $slug => $column) { ?>
							<td class="<?= $column['align'] . ' ' . $slug . ($column['editable'] ? ' editable' : ''); ?>" data-field="<?= $slug; ?>">
								<?
								if (isset($row[$slug])) {
									$value = $row[$slug];

									//Check if the raw string override has been set for this value
									if (isset($row['#' . $slug])) {
										echo $row['#' . $slug];
									} else {
										switch ($column['type']) {
											case 'text':
											case 'int':
												?>
												<?= $value; ?>
												<? break;

											case 'date':
												?>
												<?= $value === DATETIME_ZERO ? _l("Never") : $this->date->format($value, 'short'); ?>
												<? break;

											case 'datetime':
												?>
												<?= $value === DATETIME_ZERO ? _l("Never") : $this->date->format($value, 'datetime_format_long'); ?>
												<? break;

											case 'time':
												?>
												<?= $value === DATETIME_ZERO ? _l("Never") : $this->date->format($value, 'time'); ?>
												<? break;

											case 'map':
												?>
												<?= isset($column['display_data'][$value]) ? $column['display_data'][$value] : ''; ?>
												<? break;

											case 'select':
												foreach ($column['build_data'] as $key => $c_data) {
													if (isset($c_data[$column['build_config'][0]]) && $c_data[$column['build_config'][0]] == $value) {
														?>
														<?= $c_data[$column['build_config'][1]]; ?>
													<?
													}
												}
												break;

											case 'multiselect':
												foreach ($value as $v) {
													$ms_value = is_array($v) ? $v[$column['build_config'][0]] : $v;
													foreach ($column['build_data'] as $c_data) {
														if (isset($c_data[$column['build_config'][0]]) && $c_data[$column['build_config'][0]] == $ms_value) {
															echo $c_data[$column['build_config'][1]] . "<br/>";
															break;
														}
													}
												}
												break;

											case 'format':
												?>
												<?= sprintf($column['format'], $value); ?>
												<? break;

											case 'image':
												?>
												<img src="<?= $row['thumb']; ?>"/>
												<? break;

											case 'text_list':
												if (!empty($value) && is_array($value)) {
													foreach ($value as $item) {
														echo $item[$column['display_data']];
													}
												}
												break;

											default:
												break;
										}
									}
								}?>
							</td>
						<? } ?>
						<td class="center actions">
							<?= $quick_actions; ?>
						</td>
					</tr>
				<?
				}
			} else {
				?>
				<tr>
					<td class="center" colspan="<?= count($columns) + 3; ?>"><?= _l("There are no items to list."); ?></td>
				</tr>
			<? } ?>
		</tbody>
	</table>

	<? if (!empty($save_path)) { ?>
	<div class="editable-options">
		<? foreach ($columns as $field => $column) {
			if (empty($column['editable'])) {
				continue;
			} ?>

			<div class="editable-option" data-field="<?= $field; ?>">
				<div class="input">
					<? switch ($column['type']) {
						case 'text':
							?>
							<input type="text" class="input-value"/>
							<? break;

						case 'select':
						case 'radio':
						case 'checkbox':
							echo build($column['editable'], array(
								'name'   => '',
								'data'   => $column['build_data'],
								'select' => '',
								'key'    => $column['build_config'][0],
								'value'  => $column['build_config'][1],
								'#class' => 'input-value',
							));
							break;

						case 'date':
						case 'datetime':
						case 'time':
							?>
							<input type="text" class="input-value <?= $column['type'] . 'picker'; ?>" />
							<? break;
				} ?>
				</div>
			</div>
		<? } ?>

		<div class="buttons clearfix">
			<a class="cancel-edit button remove"><?= _l("X"); ?></a>
			<a class="save-edit button save" data-loading="Saving..."><?= _l("Save"); ?></a>
		</div>
	</div>
	<? } ?>

</div>

<script type="text/javascript">
	$.ac_datepicker();

	$('.zoom_hover input, .zoom_hover textarea').focus(zoom_hover_in).blur(zoom_hover_out);

	function zoom_hover_in() {
		$zoom = $(this).closest('.zoom_hover').addClass('active');
		input = $zoom.find('.input');
		value = $zoom.find('.value');
	}

	function zoom_hover_out() {
		$zoom = $(this).closest('.zoom_hover').removeClass('active');
		input = $zoom.find('.input');
		value = $zoom.find('.value');
	}

	$('.zoom_hover .clear').click(function () {
		$(this).closest('.zoom_hover').find('input, textarea').val('').trigger('keyup').trigger('change');
	});

	$('.zoom_hover.int input').keyup(function () {
		var $zoom = $(this).closest('.zoom_hover');
		var $value = $zoom.find('.value');

		low = $zoom.find('.int_low').val();
		high = $zoom.find('.int_high').val();

		if (high || low) {
			$value.html(low + ' - ' + high);
		} else {
			$value.html('<?= _l("Modify"); ?>');
		}
	});

	$('.zoom_hover.daterange input').change(function () {
		var $zoom = $(this).closest('.zoom_hover');
		var $value = $zoom.find('.value');

		start = $zoom.find('.date_start').val();
		end = $zoom.find('.date_end').val();

		if (end || start) {
			$value.html(start + ' - ' + end);
		} else {
			$value.html('<?= _l("Modify"); ?>');
		}
	});

	$('.zoom_hover.multiselect input').change(function () {
		var $zoom = $(this).closest('.zoom_hover');
		var $value = $zoom.find('.value');
		var $selected = $zoom.find(':checked');

		if ($selected.length == 0) {
			$value.html('<?= _l("Modify"); ?>');
		} else {
			var str = '';
			$selected.each(function (i, e) {
				var label = $('[for="' + $(e).attr('id') + '"]').html();

				str += (str ? ', ' : '') + (label || $(e).val());
			});
			$value.html(str.length > 20 ? str.substr(0, 20) + '...' : str);
		}
	});

	//Add jQuery datepicker
	$.ac_datepicker();

	//Add Item Selector
	var $listview = $(".table-list-view-box").not('.activated').addClass('activated');

	$listview.find('.filter-list-item').click(function () {
		cb = $(this).find('[name="batch[]"]');
		if (cb.data('clicked')) {
			cb.data('clicked', false);
		} else {
			cb.prop('checked', !cb.prop('checked')).change();
		}
	});

	$listview.find('.filter-list-item [name="batch[]"]').change(function () {
		$(this).closest('.filter-list-item').toggleClass('active', $(this).prop('checked'));
	});

	$listview.find('.filter-button').click(function () {
		var $this = $(this);
		$filter = $this.closest('.filter-list');
		$this.attr('href', $filter.apply_filter("<?= $filter_url; ?>"));
	});

	$listview.find('.reset-button').click(function () {
		var $this = $(this);
		$filter = $this.closest('.filter-list');
		$this.closest('.filter-list').find('[name]').val('');
		$this.attr('href', $filter.apply_filter("<?= $filter_url; ?>"));
	});

	$listview.find('.filter-list').keyup(function (e) {
		if (e.keyCode == 13) {
			$(this).find('.filter-button').click()[0].click();
		}
	});

	<? if (!empty($save_path)) { ?>
	$listview.find('tr.filter-list-item td.editable').click(function (event) {
		var $this = $(this);
		var field = $this.attr('data-field');

		if (field) {
			var $options = $this.closest('.table-list-view-box').find('.editable-options');
			$options.children('.show').removeClass('show');
			$options.find('[data-field="' + field + '"]').addClass('show');
			$this.append($options);
			$options.attr('data-id', $this.closest('[data-row-id]').attr('data-row-id'));
		}
	});

	$listview.find('.editable-options .save-edit').click(function () {
		var $this = $(this);
		var $box = $this.closest('.table-list-view-box');
		var $options = $this.closest('.editable-options');
		var $option = $options.find('.show');
		var $input = $option.find('.input-value');
		var field = $option.attr('data-field');
		var value = $input.val();

		var data = {
			id: $options.attr('data-id')
		}
		data[field] = value;

		$this.loading();
		$box.append($options);

		var display = value;

		if ($input.is('select')) {
			display = $input.find('option[value="'+value+'"]').html();
		}

		$box.find('[data-row-id="' + data.id + '"] td[data-field="'+field+'"]').html(display);

		$.post("<?= site_url($save_path); ?>", data, function (response) {
			$this.loading('stop');
			$listview.ac_msg(response);
		}, 'json');
	});

	$listview.find('.editable-options').click(function(event) {
		event.stopPropagation();
		return false;
	});

	$listview.find('.editable-options .cancel-edit').click(function (event) {
		var $box = $(this).closest('.table-list-view-box');
		$box.append($(this).closest('.editable-options'));
		event.stopPropagation();
		return false;
	});

	$listview.find('.editable-option .input input[type=text]').keyup(function (event) {
		if (event.keyCode == 13) {
			$(this).closest('.editable-options').find('.save-edit').click();
			event.stopPropagation();
			return false;
		}
	});
	<? } ?>
</script>
