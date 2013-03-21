<table class='form'>
<? foreach($fields as $name=>$field) { ?>
  <? if(!$this->template->option('use_' . $name, true)) continue; ?>
  <? $value = isset($$name) ? $$name : ''; ?>
  <tr>
    <td>
      <? if($this->template->option('require_' . $name)) { ;?>
      <span class="required">*</span>
      <? }?>
      <span class='form_entry'><?= ${'entry_' . $name};?></span>
    </td>
    <td>
      <? switch($field['#type']) {
        case 'text': ?>
        <input type="text" name="<?= $name;?>" value="<?= $value;?>" class="large-field" />
          <? break;
        case 'select': ?>
          <? if(isset($field['values']) && $field['values']) {?>
            <? $this->builder->set_builder_config($field['id'], $field['name']); ?>
            <?= $this->builder->build('select', $field['values'], $name, $value, array('class'=>'large-field')); ?>
          <? } else { ?>
             <select name="<?= $name;?>" class="large-field"</select>
          <? } ?>
          <? break;
        default: break;
      } ?>
    </td>
  </tr>
<? }?>
</table>