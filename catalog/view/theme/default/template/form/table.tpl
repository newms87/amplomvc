<? if($use_form_tag) {?>
<form action="<?= $action;?>" method="<?= $method;?>" <?= $form_tag_attrs;?>>
<? } ?>
<table class='form'>
<? foreach($fields as $name => $field) {
  
  if(!in_array($field['type'], array('image','button', 'submit'))){ ?>
  <tr>
    <td>
      <? if($field['required']) { ;?>
      <span class="required">*</span>
      <? }?>
      <span class='form_entry'><?= $field['display_name'];?></span>
    </td>
  </tr>
  <? }?>
  
  <tr>
    <td>
      <? if(isset($field['content_before'])) { ?>
         <?= $field['content_before'];?>
      <? } ?>
      
      <? switch($field['type']) {
           
        case 'text':
        case 'password': ?>
        <input type="<?= $field['type'];?>" name="<?= $field['name'];?>" value="<?= $field['select'];?>" <?= $field['html_attrs'];?> />
          <? break;
          
        case 'select':
          $this->builder->set_builder_config($field['builder_id'], $field['builder_name']);
          echo $this->builder->build('select', $field['values'], $field['name'], $field['select'], $field['html_attrs']);
          break;
        
        case 'radio':
          $this->builder->set_builder_config($field['builder_id'], $field['builder_name']);
          echo $this->builder->build('radio', $field['values'], $field['name'], $field['select'], $field['html_attrs']);
          break;
           
        case 'checkbox': ?>
          <? break;
        
        case 'button':
        case 'submit':
        case 'image': ?>
          <input type="<?= $field['type'];?>" name="<?= $field['name'];?>" value="<?= $field['display_name'];?>" <?= $field['html_attrs'];?> />
          <? break;
          
        default: break;
      } ?>
      
      <? if(isset($field['content_after'])){ ?>
         <?= $field['content_after'];?>
      <? } ?>
    </td>
  </tr>
<? }?>
</table>
<? if($use_form_tag) {?>
</form>
<? } ?>