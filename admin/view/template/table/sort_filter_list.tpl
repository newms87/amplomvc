<table class="list">
 <thead>
   <tr>
     <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
     <td class="center"><?= $column_action; ?></td>
     <? foreach($columns as $column) {?>
     <td class="column_title <?= $column['align'];?>">
       <? if($column['sortable']) { ?>
       <a href="index.php?route=<?=$route . ($filter_query? ('&' . $filter_query) : '');?>&sort=<?= $column['name'];?>&order=<?= ($sort == $column['name'] && $order == 'ASC') ? 'DESC' : 'ASC';?>" class="<?= $sort == $column['name'] ? strtolower($order) : '';?>"><?= $column['display_name'];?></a>
       <? } else {?>
       <?= $column['display_name'];?>
       <? } ?>
     </td>
     <? } ?>
     <td class="center"><?= $column_action; ?></td>
   </tr>
 </thead>
 <tbody>
   <tr id="filter_list">
     <td></td>
     <td align="center"><a onclick="filter();" class="button"><?= $button_filter; ?></a></td>
     <? foreach($columns as $column) { ?>
     <? if($column['filter']) { ?>
     <td class='column_filter <?= $column['align'];?>'>
       <? switch($column['filter_type']) {
         case 'text':
         case 'int': ?>
            <input type="text" name="filter[<?= $column['name'];?>]" value="<?= $column['filter_value'];?>" />
         <? break;
         
         case 'select':
				if(isset($column['filter_config'])){
					$this->builder->set_builder_config(key($column['filter_config']), current($column['filter_config']));
				}
            echo $this->builder->build('select', $column['filter_data'], "filter[$column[name]]", $column['filter_value']);
            break;
         
         case 'date_range':
         case 'time_range':
         case 'datetime_range': ?>
            <?= $entry_date_from;?> <input class='<?= str_replace('_range', '', $column['filter_type']); ?>' type="text" name="filter[<?= $column['name'];?>][start]" value="<?= isset($column['filter_value']['start']) ? $column['filter_value']['start'] : '';?>" />
            <?= $entry_date_to;?> <input class='<?= str_replace('_range', '', $column['filter_type']); ?>' type="text" name="filter[<?= $column['name'];?>][end]" value="<?= isset($column['filter_value']['end']) ? $column['filter_value']['end'] : '';?>" />
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
   <? if(!empty($table_data)) { ?>
   <? foreach ($table_data as $data) { ?>
   <tr>
     <td class="center"><input type="checkbox" name="selected[]" value="<?= $data[$row_id];?>" <?= (isset($data['selected']) && $data['selected']) ? "checked='checked'" : "";?> /></td>
     <td class="right">[ <a href="<?= $data['action']['href']; ?>"><?= $data['action']['text']; ?></a> ]</td>
     <? foreach($columns as $name => $col) {
       switch($col['display_type']) {
          case 'text':
          case 'int':?>
             <td class="<?= $col['align'];?>"><?= $data[$name]; ?></td>
          <? break;
          
          case 'date': ?>
             <td class="<?= $col['align'];?>"><?= $this->tool->format_datetime($data[$name], 'M d, Y'); ?></td>
          <? break;
          
          case 'datetime': ?>
             <td class="<?= $col['align'];?>"><?= $this->tool->format_datetime($data[$name], 'M d, Y H:i A'); ?></td>
          <? break;
          
          case 'time': ?>
             <td class="<?= $col['align'];?>"><?= $this->tool->format_datetime($data[$name], 'H:i A'); ?></td>
          <? break;
          
          case 'map': ?>
             <td class="<?= $col['align'];?>"><?= isset($col['display_data'][$data[$name]]) ? $col['display_data'][$data[$name]] : ''; ?></td>
          <? break;
          
			 case 'assoc_array': ?>
			 	 <td class="<?= $col['align'];?>">
			 	 <? if(!empty($data[$name]) && is_array($data[$name])){
	                foreach($data[$name] as $item){
	                	echo $col['display_data'][$item[$col['display_config']]];
						 }
				 	 }?>
				 </td>
			 <? break;
				 
          case 'format': ?>
             <td class="<?= $col['align'];?>"><?= sprintf($col['format'],$data[$name]); ?></td>
          <? break;
          
          case 'image': ?>
            <td class="<?= $col['align'];?>"><img src="<?= $data[$name]; ?>" /></td>
          <? break;
          
          case 'text_list': ?>
            <td class="<?= $col['align'];?>">
            <? if(!empty($data[$name]) && is_array($data[$name])){
                foreach($data[$name] as $item){
                   echo $item[$col['display_data']];
                }
             }?>
             </td>
           <? break;
          
          default: break;
       } ?>
     <? } ?>
     <td class="right">[ <a href="<?= $data['action']['href']; ?>"><?= $data['action']['text']; ?></a> ]</td>
   </tr>
   <? } ?>
   <? } else { ?>
   <tr>
     <td class="center" colspan="11"><?= $text_no_results; ?></td>
   </tr>
   <? } ?>
 </tbody>
</table>

<?=$this->builder->js('filter_url', '#filter_list', $route);?>

<?=$this->builder->js('datepicker');?>