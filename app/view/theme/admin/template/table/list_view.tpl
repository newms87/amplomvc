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
 * );
 */
?>
<? $show_actions = !empty($show_actions); ?>

<div class="table-list-view-box">
	<table class="list table-list-view">
		<thead>
		<tr>
			<? if (!empty($index)) { ?>
				<td width="1" class="center">
					<input type="checkbox" class="select-all"/>
				</td>
			<? } ?>
			<? if ($show_actions) { ?>
				<td class="center column-title">
					<span>{{Action}}</span>
				</td>
			<? } ?>
			<? foreach ($columns as $slug => $column) { ?>
				<td class="column-title <?= $column['align'] . ' ' . $slug; ?>">
					<? if ($column['sortable']) { ?>
						<a href="<?= $sort_url . '&' . http_build_query($column['sort']); ?>" class="sortable <?= $column['sort_class']; ?>"><?= $column['display_name']; ?></a>
					<? } else { ?>
						<span><?= $column['display_name']; ?></span>
					<? } ?>
				</td>
			<? } ?>
			<? if ($show_actions) { ?>
				<td class="center column-title">
					<span>{{Action}}</span>
				</td>
			<? } ?>
		</tr>
		</thead>
		<tbody>
		<tr class="filter-list <?= _get('hidefilter') ? 'hide' : ''; ?>">
			<? if (!empty($index)) { ?>
				<td></td>
			<? } ?>
			<? if ($show_actions) { ?>
				<td align="center">
					<a class="button filter-button">{{Filter}}</a>
					<? if (!empty($_GET['filter'])) { ?>
						<a class="reset reset-button">{{Reset}}</a>
					<? } ?>
					<a class="hide-filter">{{Hide}}</a>
				</td>
			<? } ?>
			<? foreach ($columns as $slug => $column) { ?>
				<? if ($column['filter']) { ?>
					<td class="column-filter <?= $column['align'] . ' ' . $slug; ?>">
						<div class="filter-type <?= !empty($column['filter_type']) ? $column['filter_type'] : ''; ?>"></div>
						<? switch ($column['filter']) {
							case 'text':
								?>
								<input placeholder="{{Search}} <?= $column['display_name']; ?>" type="text" name="filter[<?= $slug; ?>]" value="<?= $column['filter_value']; ?>"/>
								<? break;

							case 'pk':
							case 'pk-int':
							case 'int':
							case 'float':
							case 'decimal':
								if (!isset($column['filter_value']['low'])) {
									$column['filter_value']['low'] = null;
								}
								if (!isset($column['filter_value']['high'])) {
									$column['filter_value']['high'] = null;
								}
								?>
								<div class="zoom-hover int">
									<div class="input">
										<input placeholder="{{From}}" type="text" class="int_low" name="filter[<?= $slug; ?>][low]" value="<?= $column['filter_value']['low']; ?>"/>
										<input placeholder="{{To}}" type="text" class="int_high" name="filter[<?= $slug; ?>][high]" value="<?= $column['filter_value']['high']; ?>"/>
										<span class="clear">clear</span>
									</div>
									<div class="value">
										<? if ($column['filter_value']['low'] !== null || $column['filter_value']['high'] !== null) { ?>
											<?= $column['filter_value']['low'] . ' - ' . $column['filter_value']['high']; ?>
										<? } else { ?>
											{{Modify}}
										<? } ?>
									</div>
								</div>
								<? break;

							case 'select':
								echo build(array(
										'type'   => 'select',
										'name'   => "filter[$slug]",
										'select' => $column['filter_value'],
									) + $column['build']);
								break;

							case 'multiselect':
								?>
								<div class="zoom-hover multiselect">
									<div class="input">
										<?=
										build(array(
												'type'   => 'multiselect',
												'name'   => "filter[$slug]",
												'select' => $column['filter_value'],
											) + $column['build']); ?>
									</div>
									<div class="value">
										<? if (!empty($column['filter_value'])) {
											$build_value = $column['build']['value'];
											$build_label = $column['build']['label'];

											$vals = array();
											foreach ($column['filter_value'] as $v) {
												if ($build_value === false) {
													$vals[] = $build_label ? $column['build']['data'][$v][$build_label] : $column['build']['data'][$v];
												} else {
													foreach ($column['build']['data'] as $bd) {
														if (is_numeric($v) ? $bd[$build_value] == $v : $bd[$build_value] === $v) {
															$vals[] = isset($bd[$build_label]) ? $bd[$build_label] : $bd[$build_value];
														}
													}
												}
											} ?>

											<?= charlimit(implode(', ', $vals), 20, '...', false); ?>
										<? } else { ?>
											{{Modify}}
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

								<div class="zoom-hover daterange">
									<div class="input">
										<input placeholder="{{Start}}" class="date_start <?= $column['type'] . 'picker'; ?>" type="text" name="filter[<?= $slug; ?>][start]" value="<?= $column['filter_value']['start']; ?>"/>
										<input placeholder="{{End}}" class="date_end <?= $column['type'] . 'picker'; ?>" type="text" name="filter[<?= $slug ?>][end]" value="<?= $column['filter_value']['end']; ?>"/>
										<span class="clear">clear</span>
									</div>
									<div class="value" data-default="{{Date Range}}">
										<? if ($column['filter_value']['start'] !== null || $column['filter_value']['end'] !== null) { ?>
											<?= $column['filter_value']['start'] . ' - ' . $column['filter_value']['end']; ?>
										<? } else { ?>
											{{Date Range}}
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
			<? if ($show_actions) { ?>
				<td align="center">
					<a class="button filter-button">{{Filter}}</a>
					<? if (!empty($_GET['filter'])) { ?>
						<a class="reset reset-button">{{Reset}}</a>
					<? } ?>
					<a class="hide-filter">{{Hide}}</a>
				</td>
			<? } ?>
		</tr>
		<? if (!empty($rows)) { ?>
			<? foreach ($rows as $row) { ?>
				<? $row['#class'] = (!empty($row['#class']) ? $row['#class'] . ' ' : '') . 'filter-list-item'; ?>
				<tr <?= attrs($row); ?> data-row-id="<?= !empty($row[$index]) ? $row[$index] : ''; ?>">
					<? if (!empty($index)) { ?>
						<? $uniqid = uniqid($row[$index]); ?>
						<td class="center">
							<input id="rowid<?= $uniqid; ?>" type="checkbox" name="batch[]" onclick="$(this).data('clicked',true)" value="<?= $row[$index]; ?>" <?= !empty($row['selected']) ? 'checked' : ''; ?> />
						</td>
					<? } ?>

					<? if ($show_actions) { ?>
						<? $quick_actions = '';

						if (!empty($row['actions'])) {
							foreach ($row['actions'] as $key => $action) {
								$action['#class'] = (isset($action['#class']) ? $action['#class'] . ' ' : '') . 'action action-' . $key;

								if (!empty($action['ajax'])) {
									$action['#class'] .= $action['ajax'] === 'modal' ? ' colorbox' : ' ajax';
								}

								if (!empty($action['href'])) {
									$quick_actions .= "<a href=\"$action[href]\"" . attrs($action) . ">$action[text]</a>";
								} else {
									$quick_actions .= "<span " . attrs($action) . ">$action[text]</span>";

								}
							}
						}
						?>

						<td class="center actions">
							<div class="action-buttons">
								<?= $quick_actions; ?>
							</div>
						</td>
					<? } ?>

					<? foreach ($columns as $slug => $column) { ?>
						<? $value = isset($row[$slug]) ? $row[$slug] : null; ?>

						<? if ($column['editable']) {
							$column['#data-field'] = $slug;
							$column['#data-value'] = str_replace('"', '&quot;', is_array($value) ? implode(',', $value) : $value); ?>
						<? } ?>

						<td <?= attrs($column); ?>>

							<?
							//Check if the raw string override has been set for this value
							if (isset($row['#' . $slug])) {
								echo $row['#' . $slug];
							} elseif ($value !== null) {
								switch ($column['type']) {
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
										foreach ($column['build']['data'] as $key => $c_data) {
											if (isset($c_data[$column['build']['value']]) && $c_data[$column['build']['value']] == $value) {
												?>
												<?= $c_data[$column['build']['label']]; ?>
											<?
											}
										}
										break;

									case 'multiselect':
										foreach ($value as $v) {
											$ms_value = is_array($v) ? $v[$column['build']['value']] : $v;
											foreach ($column['build']['data'] as $c_data) {
												if (isset($c_data[$column['build']['value']]) && $c_data[$column['build']['value']] == $ms_value) {
													echo $c_data[$column['build']['label']] . "<br/>";
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
										<img src="<?= image(!empty($row[$slug . '_thumb']) ? $row[$slug . '_thumb'] : $value, null, option('admin_image_thumb_height', 50)); ?>"/>
										<? break;

									case 'link-image':
										?>
										<a href="<?= $value; ?>" <?= !empty($column['colorbox']) ? 'class="colorbox colorbox-photo"' : ''; ?>><img src="<?= !empty($row[$slug . '_thumb']) ? $row[$slug . '_thumb'] : $value; ?>"/></a>
										<? break;

									case 'text_list':
										if (!empty($value) && is_array($value)) {
											foreach ($value as $item) {
												echo $item[$column['display_data']];
											}
										}
										break;

									case 'text':
									case 'int':
									case 'float':
									case 'decimal':
									default:
										if (!empty($column['charlimit'])) {
											echo charlimit($value, $column['charlimit']);
										} else {
											echo $value;
										}
										break;
								}
							} ?>
						</td>
					<? } ?>
					<? if ($show_actions) { ?>
						<td class="center actions">
							<div class="action-buttons">
								<?= $quick_actions; ?>
							</div>
						</td>
					<? } ?>
				</tr>
			<?
			}
		} else {
			?>
			<tr>
				<td class="center" colspan="<?= count($columns) + 3; ?>">{{There are no items to list.}}</td>
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
							case 'select':
							case 'radio':
							case 'checkbox':
								echo build(array(
										'type'   => $column['editable'],
										'name'   => '',
										'select' => '',
										'#class' => 'input-value',
									) + $column['build']);
								break;

							case 'date':
							case 'datetime':
							case 'time':
								?>
								<input type="text" class="input-value <?= $column['type'] . 'picker'; ?>"/>
								<? break;

							case 'longtext':
								?>
								<textarea class="input-value" rows="4" cols="40"></textarea>
								<? break;

							case 'text':
							case 'int':
							case 'float':
							case 'decimal':
							default:
								?>
								<input type="text" class="input-value"/>
								<? break;
						} ?>
					</div>
				</div>
			<? } ?>

			<div class="buttons clearfix">
				<a class="cancel-form button remove">{{X}}</a>
				<a class="save-edit button save" data-loading="Saving...">{{Save}}</a>
			</div>
		</div>
	<? } ?>
</div>

<script type="text/javascript">
	(function ($) {
		$.ac_datepicker();

		var $zoom = $('.zoom-hover');

		$zoom.find('.clear').click(function () {
			$(this).closest('.zoom-hover').find('input, textarea').val('').trigger('keyup').trigger('change');
		});

		$zoom.find('input, textarea').focus(zoom_hover_in).blur(zoom_hover_out).change(zoom_hover_change).keyup(zoom_hover_keyup);

		function zoom_hover_in() {
			$zoom = $(this).closest('.zoom-hover').addClass('active');
			input = $zoom.find('.input');
			value = $zoom.find('.value');
		}

		function zoom_hover_out() {
			$zoom = $(this).closest('.zoom-hover').removeClass('active');
			input = $zoom.find('.input');
			value = $zoom.find('.value');
		}

		function zoom_hover_change() {
			var $zoom = $(this).closest('.zoom-hover');
			var $value = $zoom.find('.value');

			if ($zoom.is('.daterange')) {
				var start = $zoom.find('.date_start').val();
				var end = $zoom.find('.date_end').val();

				if (end || start) {
					$value.html(start + ' - ' + end);
				} else {
					$value.html($value.attr('data-default') || '{{Modify}}');
				}
			} else if ($zoom.is('.multiselect')) {
				var $selected = $zoom.find(':checked');

				if ($selected.length == 0) {
					$value.html($value.attr('data-default') || '{{Modify}}');
				} else {
					var str = '';
					$selected.each(function (i, e) {
						var label = $('[for="' + $(e).attr('id') + '"]').html();

						str += (str ? ', ' : '') + (label || $(e).val());
					});
					$value.html(str.length > 20 ? str.substr(0, 20) + '...' : str);
				}
			}
		}

		function zoom_hover_keyup() {
			var $zoom = $(this).closest('.zoom-hover');
			var $value = $zoom.find('.value');

			if ($zoom.is('.int')) {
				low = $zoom.find('.int_low').val();
				high = $zoom.find('.int_high').val();

				if (high || low) {
					$value.html(low + ' - ' + high);
				} else {
					$value.html($value.attr('data-default') || '{{Modify}}');
				}
			}
		}

		//Add Item Selector
		var $listview = $(".table-list-view-box").use_once();

		$listview.find('.select-all').click(function () {
			$(this).closest('.table-list-view').find('[name="batch[]"]').prop('checked', this.checked).change();
		});

		$listview.find('.filter-list-item').click(function () {
			var cb = $(this).find('[name="batch[]"]');
			if (cb.data('clicked')) {
				cb.data('clicked', false);
			} else {
				cb.prop('checked', !cb.prop('checked')).change();
			}
		});

		$listview.find('.filter-list-item [name="batch[]"]').change(function () {
			var $this = $(this);
			$this.closest('.filter-list-item').toggleClass('active', $this.prop('checked'));
		})
			.siblings('label').click(function (event) {
				var $input = $('#' + $(this).attr('for'));
				$input.prop('checked', !$input.prop('checked')).change();
				event.stopPropagation();
				return false;
			});

		function refresh_listing() {
			var $this = $(this);
			var $list = $this.hasClass('listing') ? $this : $this.closest('.listing');
			$list.find('.refresh-listing').click();
		}

		$listview.find('.filter-button').click(function () {
			var $this = $(this);
			$filter = $this.closest('.filter-list');
			$this.attr('href', $filter.apply_filter("<?= $filter_url; ?>"));
		});

		$listview.find('.filter-type').click(function () {
			var $this = $(this);
			if ($this.hasClass('not')) {
				$this.removeClass('not');
			} else if ($this.hasClass('equals')) {
				$this.removeClass('equals').addClass('not');
			} else {
				$this.addClass('equals');
			}
		});

		<? if (!empty($filter_style) && $filter_style === 'persistent') { ?>
		$listview.find('.filter-type').removeClass('not').addClass('equals').hide();

		$listview.find('.column-filter').find('input, select').on('keyup change', delay_update);

		var delay = false;

		function delay_update($filter, my_delay) {
			if (my_delay) {
				if (my_delay === delay) {
					var $widget = $filter.closest('.widget-listing').addClass('loading');

					$('#ui-datepicker-div').remove();

					$.get($filter.apply_filter("<?= $filter_url; ?>"), {}, function (response) {
						$widget.replaceWith(response);
					});
				}
			} else {
				var event = $filter;
				var my_delay = Date.now();
				var $filter = $(this).closest('.filter-list');
				delay = my_delay;

				if (event.keyCode === 13) {
					delay_update($filter, my_delay);
				} else {
					setTimeout(function () {
						delay_update($filter, my_delay)
					}, 1500);
				}
			}
		}

		<? } ?>

		$listview.find('.reset-button').click(function () {
			var $this = $(this);
			$filter = $this.closest('.filter-list');
			$filter.find('[name]').val('');
			$filter.find('.filter-type').removeClass('not equals');
			$this.attr('href', $filter.apply_filter("<?= $filter_url; ?>"));
		});

		<? if ($show_actions) { ?>
		$listview.find('.filter-list').keyup(function (e) {
			if (e.keyCode == 13) {
				$(this).find('.filter-button')[0].click();
			}
		});

		$listview.find('.hide-filter').click(function () {
			toggle_filter($(this).closest('.listing'));
		});
		<? } ?>

		$listview.find('.filter-list > td').click(function () {
			if ($(this).closest('.filter-list').hasClass('hide')) {
				toggle_filter($(this).closest('.listing'), false);
			}
		});

		function toggle_filter($listing, hide) {
			var $list = $listing.find('.filter-list');
			var $refresh = $listing.find('.refresh-listing');

			$list.toggleClass('hide', hide);
			$refresh.attr('href', $refresh.attr('href').replace(/&hidefilter=1/, '') + ($list.hasClass('hide') ? '&hidefilter=1' : ''));

			event.stopPropagation();
			return false;
		}

		<? if (!empty($save_path)) { ?>
		$listview.find('tr.filter-list-item td.editable').click(function () {
			var $this = $(this);
			var field = $this.attr('data-field');
			var value = $this.attr('data-value').replace(/&quot;/g, '"');

			if (field) {
				var $options = $this.closest('.table-list-view-box').find('.editable-options');
				$options.children('.show').removeClass('show');
				$options.find('[data-field="' + field + '"]').addClass('show').find('.input-value').val(value);
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
			var id = $options.attr('data-id');

			var data = {};
			data['<?= $index; ?>'] = id;
			data[field] = value;

			$this.loading();
			$box.append($options);

			var display = value;

			if ($input.is('select')) {
				display = $input.find('option[value="' + value + '"]').html();
			}

			var $field = $box.find('[data-row-id="' + id + '"] td[data-field="' + field + '"]').html(display);
			$field.attr('data-value', value.replace('"', '&quot;'));

			$.post("<?= site_url($save_path); ?>", data, function (response) {
				$this.loading('stop');
				$listview.show_msg(response);
			}, 'json');
		});

		$listview.find('.editable-options').click(function (event) {
			event.stopPropagation();
			return false;
		});

		$listview.find('.editable-options .cancel-form').click(function (event) {
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

		$('.action-buttons').overflown('y', 5);
	})($);
</script>
