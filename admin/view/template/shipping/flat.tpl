<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <? if ($error_warning) { ?>
  <div class="message_box warning"><?= $error_warning; ?></div>
  <? } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/shipping.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><?= $entry_cost; ?></td>
            <td><input type="text" name="flat_cost" value="<?= $flat_cost; ?>" /></td>
          </tr>
          <tr>
            <td><?= $entry_tax_class; ?></td>
            <td><select name="flat_tax_class_id">
                  <option value="0"><?= $text_none; ?></option>
                  <? foreach ($tax_classes as $tax_class) { ?>
                  <? if ($tax_class['tax_class_id'] == $flat_tax_class_id) { ?>
                  <option value="<?= $tax_class['tax_class_id']; ?>" selected="selected"><?= $tax_class['title']; ?></option>
                  <? } else { ?>
                  <option value="<?= $tax_class['tax_class_id']; ?>"><?= $tax_class['title']; ?></option>
                  <? } ?>
                  <? } ?>
                </select></td>
          </tr>
          <tr>
            <td><?= $entry_geo_zone; ?></td>
            <td><select name="flat_geo_zone_id">
                <option value="0"><?= $text_all_zones; ?></option>
                <? foreach ($geo_zones as $geo_zone) { ?>
                <? if ($geo_zone['geo_zone_id'] == $flat_geo_zone_id) { ?>
                <option value="<?= $geo_zone['geo_zone_id']; ?>" selected="selected"><?= $geo_zone['name']; ?></option>
                <? } else { ?>
                <option value="<?= $geo_zone['geo_zone_id']; ?>"><?= $geo_zone['name']; ?></option>
                <? } ?>
                <? } ?>
              </select></td>
          </tr>
          <tr>
            <td><?= $entry_status; ?></td>
            <td><select name="flat_status">
                <? if ($flat_status) { ?>
                <option value="1" selected="selected"><?= $text_enabled; ?></option>
                <option value="0"><?= $text_disabled; ?></option>
                <? } else { ?>
                <option value="1"><?= $text_enabled; ?></option>
                <option value="0" selected="selected"><?= $text_disabled; ?></option>
                <? } ?>
              </select></td>
          </tr>
          <tr>
            <td><?= $entry_sort_order; ?></td>
            <td><input type="text" name="flat_sort_order" value="<?= $flat_sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?= $footer; ?> 