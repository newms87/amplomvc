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
 * 	'build_data'	=> mixed (optoinal) - Fill data for the column. Used to display a key value as text, or with a filter depending on the 'type'.
 * 	'build_config' => array (optional) - Use this with 'build_data' to specify the array( $key => $value ) for the builder tool
 * );
 */
?>
<table class="list">
<thead>
<tr>
	<? if (!empty($row_id)) { ?>
	<td width="1" class="center"><input type="checkbox" onclick="$('[name=\'selected[]\']').prop('checked', this.checked).change();"/></td>
	<? } ?>
	<td class="center column_title"><span><?= $column_action; ?></span></td>
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
	<td class="center column_title"><span><?= $column_action; ?></span></td>
</tr>
</thead>
<tbody>
<tr id="filter_list">
	<? if (!empty($row_id)) { ?>
	<td></td>
	<? } ?>
	<td align="center">
		<a onclick="return apply_filter();" class="button"><?= $button_filter; ?></a>
		<? if (!empty($_GET['filter'])) { ?>
			<a onclick="return reset_filter();" class="reset"><?= $button_reset; ?></a>
		<? } ?>
	</td>
	<? foreach ($columns as $slug => $column) { ?>
		<? if ($column['filter']) { ?>
			<td class="column_filter <?= $column['align'] . ' ' . $slug; ?>">
				<? switch ($column['type']) {
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
								<input type="text" class="int_low" name="filter[<?= $slug; ?>][low]"
								       value="<?= $column['filter_value']['low']; ?>"/>
								<input type="text" class="int_high" name="filter[<?= $slug; ?>][high]"
								       value="<?= $column['filter_value']['high']; ?>"/>
								<span class="clear">clear</span>
							</div>
							<div class="value">
								<? if (!is_null($column['filter_value']['low']) || !is_null($column['filter_value']['high'])) { ?>
									<?= $column['filter_value']['low'] . ' - ' . $column['filter_value']['high']; ?>
								<? } else { ?>
									<?= $text_modify_filter; ?>
								<? } ?>
							</div>
						</div>
						<? break;
					case 'select':
					case 'multiselect':
						if (isset($column['build_config'])) {
							$this->builder->set_config($column['build_config']);
						}
						$blank_option = $column['filter_blank'] ? array('' => '') : array();
						echo $this->builder->build('select', $blank_option + $column['build_data'], "filter[$slug]", $column['filter_value']);
						break;

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
								<input class="date_start <?= $column['type'] . 'picker'; ?>" type="text"
								       name="filter[<?= $slug; ?>][start]" value="<?= $column['filter_value']['start']; ?>"/>
								<input class="date_end <?= $column['type'] . 'picker'; ?>" type="text"
								       name="filter[<?= $slug ?>][end]" value="<?= $column['filter_value']['end']; ?>"/>
								<span class="clear">clear</span>
							</div>
							<div class="value">
								<? if (!is_null($column['filter_value']['start']) || !is_null($column['filter_value']['end'])) { ?>
									<?= $column['filter_value']['start'] . ' - ' . $column['filter_value']['end']; ?>
								<? } else { ?>
									<?= $text_modify_filter; ?>
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
		<a onclick="return apply_filter();" class="button"><?= $button_filter; ?></a>
		<? if (!empty($_GET['filter'])) { ?>
			<a onclick="return reset_filter();" class="reset"><?= $button_reset; ?></a>
		<? } ?>
	</td>
</tr>
<? if (!empty($rows)) { ?>
	<? foreach ($rows as $row) { ?>
		<tr class="filter_list_item">
			<? if (!empty($row_id)) { ?>
			<td class="center">
				<input type="checkbox" name="selected[]" onclick="$(this).data('clicked',true)" value="<?= $row[$row_id]; ?>" <?= (isset($row['selected']) && $row['selected']) ? "checked='checked'" : ""; ?> />
			</td>
			<? } ?>

			<? $quick_actions = '';
			foreach ($row['actions'] as $key => $action) {
				$action['#class'] = (isset($action['#class']) ? $action['#class'] . ' ' : '') . 'action action-' . $key;
				if (!empty($action['href'])) {
					$quick_actions .= "<a href=\"$action[href]\"" . $this->builder->attrs($action) . ">$action[text]</a>";
				} else {
					$quick_actions .= "<span " . $this->builder->attrs($action) . ">$action[text]</span>";
				}
			} ?>

			<td class="center actions">
				<?= $quick_actions; ?>
			</td>
			<? foreach ($columns as $slug => $column) {
				if (!isset($row[$slug])) {
					?>
					<td></td>
					<? continue;
				}
				?>
				<td class="<?= $column['align'] . ' ' . $slug; ?>">
				<span>
			<?

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
						<?= $this->date->format($value, 'short'); ?>
						<? break;

					case 'datetime':
						?>
						<?= $this->date->format($value, 'datetime_format_long'); ?>
						<? break;

					case 'time':
						?>
						<?= $this->date->format($value, 'time'); ?>
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
			}?>
				</span>
				</td>
			<? } ?>
			<td class="center actions">
				<?= $quick_actions; ?>
			</td>
		</tr>
	<? } ?>
<? } else { ?>
	<tr>
		<td class="center" colspan="<?= count($columns) + 3; ?>"><?= $text_no_results; ?></td>
	</tr>
<? } ?>
</tbody>
</table>

<script type="text/javascript">//<!--
	$("#filter_list").keydown(function (e) {
		if (e.keyCode == 13) {
			return apply_filter();
		}
	});

	$('.zoom_hover input, .zoom_hover textarea').focus(zoom_hover_in).blur(zoom_hover_out);

	function zoom_hover_in() {
		zoom = $(this).closest('.zoom_hover').addClass('active');
		input = zoom.find('.input');
		value = zoom.find('.value');
	}

	function zoom_hover_out() {
		zoom = $(this).closest('.zoom_hover').removeClass('active');
		input = zoom.find('.input');
		value = zoom.find('.value');
	}

	$('.zoom_hover .clear').click(function () {
		$(this).closest('.zoom_hover').find('input, textarea').val('').trigger('keyup').trigger('change');
	});

	$('.zoom_hover.int input').keyup(function () {
		zoom = $(this).closest('.zoom_hover');
		value = zoom.find('.value');

		low = zoom.find('.int_low').val();
		high = zoom.find('.int_high').val();

		if (high || low) {
			value.html(low + ' - ' + high);
		} else {
			value.html('<?= $text_modify_filter; ?>');
		}
	});

	$('.zoom_hover.daterange input').change(function () {
		zoom = $(this).closest('.zoom_hover');
		value = zoom.find('.value');

		start = zoom.find('.date_start').val();
		end = zoom.find('.date_end').val();

		if (end || start) {
			value.html(start + ' - ' + end);
		} else {
			value.html('<?= $text_modify_filter; ?>');
		}
	});

	//Add jQuery datepicker
	$.ac_datepicker();

	//Add Item Selector
	$('.filter_list_item').click(function(){
		cb = $(this).find('[name="selected[]"]');
		if (cb.data('clicked')) {
			cb.data('clicked',false);
		} else {
			cb.prop('checked', !cb.prop('checked')).change();
		}
	});

	$('.filter_list_item [name="selected[]"]').change(function(){
		$(this).closest('.filter_list_item').toggleClass('active', $(this).prop('checked'));
	});

	function apply_filter() {
		$('#filter_list').apply_filter("<?= $this->url->link($this->url->getPath(), $this->url->getQueryExclude('filter')); ?>");
		return false;
	}

	function reset_filter() {
		$('#filter_list').find('[name]').val('');
		return apply_filter();
	}
//--></script>