<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <? if ($error_warning) { ?>
  <div class="message_box warning"><?= $error_warning; ?></div>
  <? } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/information.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?= $entry_name; ?></td>
            <td><? foreach ($languages as $language) { ?>
              <input type="text" name="attribute_description[<?= $language['language_id']; ?>][name]" value="<?= isset($attribute_description[$language['language_id']]) ? $attribute_description[$language['language_id']]['name'] : ''; ?>" />
              <img src="view/image/flags/<?= $language['image']; ?>" title="<?= $language['name']; ?>" /><br />
              <? if (isset($error_name[$language['language_id']])) { ?>
              <span class="error"><?= $error_name[$language['language_id']]; ?></span><br />
              <? } ?>
              <? } ?></td>
          </tr>
          <tr>
            <td><?= $entry_attribute_group; ?></td>
            <td><select name="attribute_group_id">
                <? foreach ($attribute_groups as $attribute_group) { ?>
                <? if ($attribute_group['attribute_group_id'] == $attribute_group_id) { ?>
                <option value="<?= $attribute_group['attribute_group_id']; ?>" selected="selected"><?= $attribute_group['name']; ?></option>
                <? } else { ?>
                <option value="<?= $attribute_group['attribute_group_id']; ?>"><?= $attribute_group['name']; ?></option>
                <? } ?>
                <? } ?>
              </select></td>
          </tr>
          <tr>
            <td><?= $entry_sort_order; ?></td>
            <td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?= $footer; ?>