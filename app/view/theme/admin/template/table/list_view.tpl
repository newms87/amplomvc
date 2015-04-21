<? /*
 * Name: list_view
 *
 * This table uses the following variables:
 *
 * $columns => array(
 * 	'type' 			=> string (required) - Input and display type. Can be 'text', 'select', 'multiselect', 'image', 'int', 'date_range', 'time_range', 'datetime_range', 'format'.
 * 	'display_name' => string (required) - Display Name for the header of the column.
 * 	'align' 			=> string (optional) - 'center' (default), 'left', 'right'.
 * 	'sort'		   => bool (optional) - Can this column can be sorted? Default is false.
 * 	'filter'			=> bool (optional) - Can this column be filtered? Default is false.
 * 	'filter_value' => mixed (optional) - Use this to override the filter value. Value is set if user has specified, otherwise the default filter value.
 * );
 */
?>
<? $show_actions = !empty($show_actions); ?>

<div class="list-view" data-index="<?= $index; ?>" data-filter-url="<?= site_url($listing_path, _get_exclude('filter', 'page')); ?>" data-filter-style="<?= $filter_style; ?>" data-save-url="<?= site_url($save_path); ?>">
	<table class="list table-list-view">
		<thead>
		<tr>
			<? if ($index) { ?>
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
					<? if ($column['sort']) { ?>
						<a href="<?= site_url($listing_path, array('sort' => $column['sort']) + _get_exclude('sort', 'page')); ?>" class="sortable <?= $column['sort_class']; ?>"><?= $column['display_name']; ?></a>
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
			<? if ($index) { ?>
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
					<? if ($index) { ?>
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
	$('.list-view').listview();
</script>
