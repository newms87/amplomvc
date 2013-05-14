<? if($show_tag) { ?>
<form action="<?= $action;?>" method="<?= $method;?>">
<? }?>

<table id="<?= $form_id; ?>" class='form <?= $form_name; ?>'>
<? foreach($fields as $name => $field) { ?>
	<? if($name == 'default' || $name == 'submit_address') continue; ?>
	<tr>
		<td>
			<? if($field['required']) { ?>
			<span class="required">*</span>
			<? }?>
			<span class='form_entry'><?= $field['display_name']; ?></span>
		</td>
		<td>
		<? switch($field['type']) {
				
			case 'text': ?>
				<input type="text" name="<?= $name; ?>" value="<?= $field['value']; ?>" />
			<? break;
			
			case 'select': ?>
					<? if(!empty($field['options'])) {?>
						<? $this->builder->set_config(key($field['build_config']), current($field['build_config'])); ?>
						<?= $this->builder->build('select', $field['options'], $name, $field['value'], $field['attrs']); ?>
					<? } else { ?>
						 <select name="<?= $name;?>" <?= $field['html_attrs']; ?>></select>
					<? } ?>
					<? break;
					
			default: break;
		} ?>
		</td>
	</tr>
<? }?>
	<tr class="address_bottom_section">
		<td>
			<? if(!empty($fields['default'])) { ?>
			<div class="set_default_address">
				<div><?= $fields['default']['display_name']; ?></div>
				<?= $this->builder->build('radio', $fields['default']['options'], 'default', $fields['default']['value']); ?>
			</div>
			<? } ?>
		</td>
		<td>
			<? if(!empty($fields['submit_address'])) { ?>
			<input type="submit" name="submit_address" class="button" value="<?= $fields['submit_address']['display_name']; ?>" />
			<? } ?>
		</td>
	</tr>	
	
</table>

<? if($show_tag) { ?>
</form>
<? } ?>

<?=$this->builder->js('load_zones', "#$form_id", '.country_select', '.zone_select');?>