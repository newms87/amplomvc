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
		<? foreach($columns as $column) {?>
		<td class="column_title <?= $column['align'];?>">
			<? if($column['sortable']) {
				$c_order = ($sort == $column['sort_value'] && $order == 'ASC') ? 'DESC' : 'ASC';
				$class = $sort == $column['sort_value'] ? strtolower($order) : '';
				?>
				<a href="<?= $sort_url; ?>&sort=<?= $column['sort_value']; ?>&order=<?= $c_order; ?>" class="sortable <?= $class; ?>"><?= $column['display_name']; ?></a>
			<? } else {?>
				<span><?= $column['display_name'];?></span>
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
		<td class='column_filter <?= $column['align'];?>'>
			<? switch($column['type']) {
				case 'text': ?>
					<input type="text" name="filter[<?= $slug;?>]" value="<?= $column['filter_value'];?>" />
				<? break;
				
				case 'int': ?>
					<div class="filter_number_range"><input type="text" name="filter[<?= $slug; ?>]" value="<?= $column['filter_value']; ?>" /></div> 
				<? break;
				case 'select':
				case 'multiselect':
					if(isset($column['build_config'])){
						$this->builder->set_config(key($column['build_config']), current($column['build_config']));
					}
					$blank_option = $column['filter_blank'] ? array(''=>'') : array();
					echo $this->builder->build('select', $blank_option + $column['build_data'], "filter[$slug]", $column['filter_value']);
					break;
				
				case 'date':
				case 'time':
				case 'datetime': ?>
					<label class="date_from"><?= $entry_date_from;?></label><input class='<?= str_replace('_range', '', $column['type']); ?>' type="text" name="filter[<?= $slug;?>][start]" value="<?= isset($column['value']['start']) ? $column['value']['start'] : '';?>" />
					<label class="date_to"><?= $entry_date_to;?></label><input class='<?= str_replace('_range', '', $column['type']); ?>' type="text" name="filter[<?= $slug?>][end]" value="<?= isset($column['value']['end']) ? $column['value']['end'] : '';?>" />
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
	<? if(!empty($data)) { ?>
	<? foreach ($data as $data) { ?>
	<tr>
		<td class="center"><input type="checkbox" name="selected[]" value="<?= $data[$row_id];?>" <?= (isset($data['selected']) && $data['selected']) ? "checked='checked'" : "";?> /></td>
		
		<? $quick_actions = '';
		foreach($data['actions'] as $key => $action){
			$action['#class'] = (isset($action['#class']) ? $action['#class'] . ' ' : '') . 'action-' . $key;
			$quick_actions .= "[ <a href=\"$action[href]\"" . $this->builder->attrs($action) . ">$action[text]</a> ]";
		} ?>
		
		<td class="center actions">
			<?= $quick_actions ;?>
		</td>
		<? foreach($columns as $slug => $column) {
			if(!isset($data[$slug])){?>
				<td></td>
				<? continue;
			}
			?>
			<td class="<?= $column['align'];?>">
			<?
			
			$value = $data[$slug];
			
			//Check if the raw string override has been set for this value
			if(isset($data['#' . $slug])){
				echo $data['#' . $slug];
			}
			else{
				switch($column['type']) {
					case 'text':
					case 'int':?>
						<?= $value; ?>
					<? break;
					
					case 'date': ?>
						<?= $this->tool->format_datetime($value, 'M d, Y'); ?>
					<? break;
					
					case 'datetime': ?>
						<?= $this->tool->format_datetime($value, 'M d, Y H:i A'); ?>
					<? break;
					
					case 'time': ?>
						<?= $this->tool->format_datetime($value, 'H:i A'); ?>
					<? break;
					
					case 'map': ?>
						<?= isset($column['display_data'][$value]) ? $column['display_data'][$value] : ''; ?>
					<? break;
					
					case 'select':
						foreach($column['build_data'] as $c_data){
							if($c_data[key($column['build_config'])] == $value){ ?>
								<?= $c_data[current($column['build_config'])];?>
							<? }
						}
						break;
						
					case 'multiselect':
						foreach($value as $v){
							$ms_value = is_array($v) ? $v[key($column['build_config'])] : $v;
							foreach($column['build_data'] as $c_data){
								if($c_data[key($column['build_config'])] == $ms_value){
									echo $c_data[current($column['build_config'])] . "<br/>";
									break;
								}
							}
						}
						break;
						
					case 'format': ?>
						<?= sprintf($column['format'],$value); ?>
					<? break;
					
					case 'image': ?>
						<img src="<?= $data['thumb']; ?>" />
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
		<td class="center" colspan="<?= count($columns) + 3;?>"><?= $text_no_results; ?></td>
	</tr>
	<? } ?>
 </tbody>
</table>

<?=$this->builder->js('filter_url', '#filter_list', $route);?>

<?=$this->builder->js('datepicker');?>