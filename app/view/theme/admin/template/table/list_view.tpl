<? /*
 * Name: list_view
 *
 * This table uses the following variables:
 *
 * $columns => array(
 * 	'type' 			=> string (required) - Input and display type. Can be 'text', 'select', 'multiselect', 'image', 'int', 'date_range', 'time_range', 'datetime_range', 'format'.
 * 	'label' => string (required) - Display Name for the header of the column.
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
				<td width="1" class="select-all-col select-col center">
					<input type="checkbox" class="select-all"/>
				</td>
			<? } ?>
			<? if ($show_actions) { ?>
				<td class="center column-title">
					<span>{{Action}}</span>
				</td>
			<? } ?>
			<? foreach ($columns as $slug => $column) { ?>
				<td class="column-title <?= $column['align'] . ' col-' . $slug; ?>">
					<? if ($column['sort']) { ?>
						<a href="<?= site_url($listing_path, array('sort' => $column['sort']) + _get_exclude('sort', 'page')); ?>" class="sortable <?= $column['sort_class']; ?>" title="<?= $slug; ?>"><?= $column['label']; ?></a>
					<? } else { ?>
						<span><?= $column['label']; ?></span>
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

		<? if ($filter_style) { ?>
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
								case 'textarea':
									?>
									<input placeholder="{{Search}} <?= $column['label']; ?>" type="text" name="filter[<?= $column['filter_key']; ?>]" value="<?= $column['filter_value']; ?>"/>
									<? break;

								case 'key':
								case 'pk':
								case 'pk-int': ?>
									<input placeholder="{{Find}} <?= $column['label']; ?>" type="text" name="filter[<?= $column['filter_key']; ?>]" value="<?= is_array($column['filter_value']) ? current($column['filter_value']) : $column['filter_value']; ?>"/>
									<? break;
								case 'int':
								case 'float':
								case 'decimal':
									if (!isset($column['filter_value']['gte'])) {
										$column['filter_value']['gte'] = null;
									}
									if (!isset($column['filter_value']['lte'])) {
										$column['filter_value']['lte'] = null;
									}
									?>
									<div class="zoom-hover int">
										<div class="input">
											<input placeholder="{{From}}" type="text" class="number-from" name="filter[<?= $column['filter_key']; ?>][gte]" value="<?= $column['filter_value']['gte']; ?>"/>
											<input placeholder="{{To}}" type="text" class="number-to" name="filter[<?= $column['filter_key']; ?>][lte]" value="<?= $column['filter_value']['lte']; ?>"/>
											<span class="clear">clear</span>
										</div>
										<div class="value">
											<? if ($column['filter_value']['gte'] !== null || $column['filter_value']['lte'] !== null) { ?>
												<?= $column['filter_value']['gte'] . ' - ' . $column['filter_value']['lte']; ?>
											<? } else { ?>
												{{Modify}}
											<? } ?>
										</div>
									</div>
									<? break;

								case 'select':
									echo build(array(
											'type'   => 'select',
											'name'   => "filter[{$column['filter_key']}]",
											'select' => $column['filter_value'],
										) + $column['build']);
									break;

								case 'multiselect':
									echo build(array(
											'type'   => 'multiselect',
											'name'   => "filter[{$column['filter_key']}]",
											'select' => $column['filter_value'],
											'#class' => 'amp-select',
										) + $column['build']);
									break;

								case 'date':
								case 'time':
								case 'datetime':
									$f = $column['filter_value'];
									?>

									<div class="amp-filter amp-filter-date zoom-hover daterange">
										<div class="input">
											<div class="amp-filter-options">
												<div class="amp-filter-option amp-filter-date-range <?= $f['eq'] ? '' : 'is-active'; ?>" data-filter-name="{{Range}}">
													<input placeholder="{{Start}}" class="date_start <?= $column['type'] . 'picker'; ?>" type="text" name="filter[<?= $column['filter_key']; ?>][gte]" value="<?= $f['gte']; ?>"/>
													<input placeholder="{{End}}" class="date_end <?= $column['type'] . 'picker'; ?>" type="text" name="filter[<?= $column['filter_key'] ?>][lte]" value="<?= $f['lte']; ?>"/>
												</div>
												<div class="amp-filter-option amp-filter-equals on-dormant <?= $f['eq'] ? 'is-active' : ''; ?>" data-filter-name="{{Equals}}">
													<input placeholder="{{Enter Date}}" class="date_equals datepicker" type="text" name="filter[<?= $column['filter_key']; ?>][eq]" value="<?= $f['eq']; ?>"/>
												</div>
											</div>

											<div class="row amp-filter-buttons">
												<div class="col xs-6 left">
													<a class="clear">{{clear}}</a>
												</div>
												<? if (user_is('Top Administrator')) { ?>
													<div class="col xs-6 right">
														<a class="amp-filter-toggle">{{Range}}</a>
													</div>
												<? } ?>
											</div>
										</div>
										<div class="value row" data-default="{{Date Range}}">
											<? if ($f['eq']) {
												echo $f['eq'];
											} else if ($f['gte'] !== null || $f['lte'] !== null) {
												echo $f['gte'] . ' - ' . $f['lte'];
											} else { ?>
												<b class="fa fa-calendar"></b>
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
		<? } ?>

		<? if (!empty($rows)) { ?>
			<? foreach ($rows as $row) { ?>
				<? $row['#class'] = (!empty($row['#class']) ? $row['#class'] . ' ' : '') . 'filter-list-item'; ?>
				<tr <?= attrs($row); ?> data-row-id="<?= !empty($row[$index]) ? $row[$index] : ''; ?>">
					<? if ($index) { ?>
						<? $uniqid = uniqid($row[$index]); ?>
						<td class="center select-col">
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
										<?= $value === DATETIME_ZERO ? _l("Never") : $r->date->format($value, 'short'); ?>
										<? break;

									case 'datetime':
										?>
										<?= $value === DATETIME_ZERO ? _l("Never") : $r->date->format($value, 'datetime_format_long'); ?>
										<? break;

									case 'time':
										?>
										<?= $value === DATETIME_ZERO ? _l("Never") : $r->date->format($value, 'time'); ?>
										<? break;

									case 'map':
										?>
										<?= isset($column['display_data'][$value]) ? $column['display_data'][$value] : ''; ?>
										<? break;

									case 'select':
										$display_value = '';

										foreach ($column['build']['data'] as $key => $c_data) {
											if (isset($c_data[$column['build']['value']]) && $c_data[$column['build']['value']] == $value) {
												$display_value .= $c_data[$column['build']['label']];
											}
										}

										echo $display_value ? $display_value : "[" . $value . "]";
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
									case 'textarea':
									case 'int':
									case 'float':
									case 'decimal':
									default:
										if (!empty($column['charlimit'])) {
											$text = charlimit($value, $column['charlimit']);
										} else {
											$text = $value;
										}

										if (_is_object($text)) {
											$text = json_encode($text);
										} elseif (!preg_match("/<[a-z]+/i", $text)) {
											$text = nl2br($text);
										}

										echo $text;
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
							case 'textarea':
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

			<div class="buttons row">
				<div class="col xs-4 remove-col xs-left">
					<a class="cancel-form button remove">{{X}}</a>
				</div>
				<div class="col xs-8 save-col xs-right">
					<a class="save-edit button save" data-loading="Saving...">{{Save}}</a>
				</div>
			</div>
		</div>
	<? } ?>
</div>

<script type="text/javascript">
	$('.list-view').listview();
</script>
