<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <? if ($error_warning) { ?>
  <div class="message_box warning"><?= $error_warning; ?></div>
  <? } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/payment.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?= $entry_merchant; ?></td>
            <td><input type="text" name="authorizenet_merchant" value="<?= $authorizenet_merchant; ?>" />
              <? if ($error_merchant) { ?>
              <span class="error"><?= $error_merchant; ?></span>
              <? } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?= $entry_key ?></td>
            <td><input type="text" name="authorizenet_key" value="<?= $authorizenet_key; ?>" />
              <? if ($error_key) { ?>
              <span class="error"><?= $error_key; ?></span>
              <? } ?></td>
          </tr>
          <tr>
            <td><?= $entry_callback; ?></td>
            <td><textarea cols="40" rows="5"><?= $callback; ?></textarea></td>
          </tr>
          <tr>
            <td><?= $entry_test; ?></td>
            <td><? if ($authorizenet_test) { ?>
              <input type="radio" name="authorizenet_test" value="1" checked="checked" />
              <?= $text_yes; ?>
              <input type="radio" name="authorizenet_test" value="0" />
              <?= $text_no; ?>
              <? } else { ?>
              <input type="radio" name="authorizenet_test" value="1" />
              <?= $text_yes; ?>
              <input type="radio" name="authorizenet_test" value="0" checked="checked" />
              <?= $text_no; ?>
              <? } ?></td>
          </tr>
          <tr>
            <td><?= $entry_total; ?></td>
            <td><input type="text" name="authorizenet_total" value="<?= $authorizenet_total; ?>" /></td>
          </tr>
          <tr>
            <td><?= $entry_order_status; ?></td>
            <td><select name="authorizenet_order_status_id">
                <? foreach ($order_statuses as $order_status) { ?>
                <? if ($order_status['order_status_id'] == $authorizenet_order_status_id) { ?>
                <option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
                <? } else { ?>
                <option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
                <? } ?>
                <? } ?>
              </select></td>
          </tr>
          <tr>
            <td><?= $entry_geo_zone; ?></td>
            <td><select name="authorizenet_geo_zone_id">
                <option value="0"><?= $text_all_zones; ?></option>
                <? foreach ($geo_zones as $geo_zone) { ?>
                <? if ($geo_zone['geo_zone_id'] == $authorizenet_geo_zone_id) { ?>
                <option value="<?= $geo_zone['geo_zone_id']; ?>" selected="selected"><?= $geo_zone['name']; ?></option>
                <? } else { ?>
                <option value="<?= $geo_zone['geo_zone_id']; ?>"><?= $geo_zone['name']; ?></option>
                <? } ?>
                <? } ?>
              </select></td>
          </tr>
          <tr>
            <td><?= $entry_status; ?></td>
            <td><select name="authorizenet_status">
                <? if ($authorizenet_status) { ?>
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
            <td><input type="text" name="authorizenet_sort_order" value="<?= $authorizenet_sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?= $footer; ?> 