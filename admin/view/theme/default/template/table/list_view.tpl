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
		<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
		<td class="center column_title"><span><?= $column_action; ?></span></td>
		<? foreach($columns as $column) { ?>
		<td class="column_title <?= $column['align']; ?>">
			<? if($column['sortable']) {
				$c_order = ($sort === $column['sort_value'] && $order === 'ASC') ? 'DESC' : 'ASC';
				$class = $sort === $column['sort_value'] ? strtolower($order) : '';
				?>
				<a href="<?= $sort_url; ?>&sort=<?= $column['sort_value']; ?>&order=<?= $c_order; ?>" class="sortable <?= $class; ?>"><?= $column['display_name']; ?></a>
			<? } else {?>
				<span><?= $column['display_name']; ?></span>
			<? } ?>
		</td>
		<? } ?>
		<td class="center column_title"><span><?= $column_action; ?></span></td>
	</tr>
 </thead>
 <tbody>
	<tr id="filter_list">
		<td></td>
		<td align="center"><a onclick="filter();" class="button"><?= $button_filter; ?></a></td>
		<? foreach($columns as $slug => $column) { ?>
		<? if($column['filter']) { ?>
		<td class='column_filter <?= $column['align']; ?>'>
			<? switch($column['type']) {
				case 'text': ?>
					<input type="text" name="filter[<?= $slug; ?>]" value="<?= $column['filter_value']; ?>" />
				<? break;
				
				case 'int': ?>
					<div class="zoom_hover">
						<div class="input filter_number_range"><input type="text" name="filter[<?= $slug; ?>]" value="<?= $column['filter_value']; ?>" /></div>
						<div class="value"><?= $column['filter_value']; ?></div>
					</div>
				<? break;
				case 'select':
				case 'multiselect':
					if(isset($column['build_config'])){
						$this->builder->set_config($column['build_config']);
					}
					$blank_option = $column['filter_blank'] ? array(''=>'') : array();
					echo $this->builder->build('select', $blank_option + $column['build_data'], "filter[$slug]", $column['filter_value']);
					break;
				
				case 'date':
				case 'time':
				case 'datetime': ?>
					<label class="date_from"><?= $entry_date_from; ?></label><input class='<?= str_replace('_range', '', $column['type']); ?>' type="text" name="filter[<?= $slug; ?>][start]" value="<?= isset($column['filter_value']['start']) ? $column['filter_value']['start'] : ''; ?>" />
					<label class="date_to"><?= $entry_date_to; ?></label><input class='<?= str_replace('_range', '', $column['type']); ?>' type="text" name="filter[<?= $slug?>][end]" value="<?= isset($column['filter_value']['end']) ? $column['filter_value']['end'] : ''; ?>" />
				<? break;
				
				default: break;
			} ?>
		</td>
		<? } else { ?>
		<td></td>
		<? } ?>
		<? } ?>
		<td align="center"><a onclick="filter();" class="button"><?= $button_filter; ?></a></td>
	</tr>
	<? if(!empty($rows)) { ?>
	<? foreach ($rows as $row) { ?>
	<tr>
		<td class="center"><input type="checkbox" name="selected[]" value="<?= $row[$row_id]; ?>" <?= (isset($row['selected']) && $row['selected']) ? "checked='checked'" : ""; ?> /></td>
		
		<? $quick_actions = '';
		foreach($row['actions'] as $key => $action){
			$action['#class'] = (isset($action['#class']) ? $action['#class'] . ' ' : '') . 'action-' . $key;
			$quick_actions .= "[ <a href=\"$action[href]\"" . $this->builder->attrs($action) . ">$action[text]</a> ]";
		} ?>
		
		<td class="center actions">
			<?= $quick_actions ; ?>
		</td>
		<? foreach($columns as $slug => $column) {
			if(!isset($row[$slug])){?>
				<td></td>
				<? continue;
			}
			?>
			<td class="<?= $column['align']; ?>">
			<?
			
			$value = $row[$slug];
			
			//Check if the raw string override has been set for this value
			if(isset($row['#' . $slug])){
				echo $row['#' . $slug];
			}
			else{
				switch($column['type']) {
					case 'text':
					case 'int':?>
						<?= $value; ?>
					<? break;
					
					case 'date': ?>
						<?= $this->date->format($value, 'short'); ?>
					<? break;
					
					case 'datetime': ?>
						<?= $this->date->format($value, 'datetime_format_long'); ?>
					<? break;
					
					case 'time': ?>
						<?= $this->date->format($value, 'time'); ?>
					<? break;
					
					case 'map': ?>
						<?= isset($column['display_data'][$value]) ? $column['display_data'][$value] : ''; ?>
					<? break;
					
					case 'select':
						foreach($column['build_data'] as $c_data){
							if(isset($c_data[$column['build_config'][0]]) && $c_data[$column['build_config'][0]] == $value){ ?>
								<?= $c_data[$column['build_config'][1]]; ?>
							<? }
						}
						break;
						
					case 'multiselect':
						foreach($value as $v){
							$ms_value = is_array($v) ? $v[$column['build_config'][0]] : $v;
							foreach($column['build_data'] as $c_data){
								if(isset($c_data[$column['build_config'][0]]) && $c_data[$column['build_config'][0]] == $ms_value){
									echo $c_data[$column['build_config'][1]] . "<br/>";
									break;
								}
							}
						}
						break;
					
					case 'format': ?>
						<?= sprintf($column['format'],$value); ?>
					<? break;
					
					case 'image': ?>
						<img src="<?= $row['thumb']; ?>" />
					<? break;
					
					case 'text_list':
						if(!empty($value) && is_array($value)){
							foreach($value as $item){
								echo $item[$column['display_data']];
							}
						}
						break;
					
					default: break;
				}
			}?>
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
$("#filter_list").keydown(function(e){
	if (e.keyCode == 13) {
		filter();
		return false;
	}
});

$('.zoom_hover').hover(zoom_hover_in, zoom_hover_out);

function zoom_hover_in(){
	input = $(this).find('.input');
	value = $(this).find('.value');
	
	input.hide();
	value.show();
}

function zoom_hover_out(){
	input = $(this).find('.input');
	value = $(this).find('.value');
	
	input.show();
	value.hide();
}
//--></script>

<?= $this->builder->js('filter_url', '#filter_list'); ?>

<?= $this->builder->js('datepicker'); ?>