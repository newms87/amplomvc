<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <?= $this->builder->display_errors($errors);?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/setting.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <div id="tabs" class="htabs"><a href="#tab-general"><?= $tab_general; ?></a><a href="#tab-store"><?= $tab_store; ?></a><a href="#tab-local"><?= $tab_local; ?></a><a href="#tab-option"><?= $tab_option; ?></a><a href="#tab-image"><?= $tab_image; ?></a><a href="#tab-server"><?= $tab_server; ?></a></div>
      <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tab-general">
          <table class="form">
            <tr>
              <td><span class="required">*</span> <?= $entry_url; ?></td>
              <td><input type="text" name="config_url" value="<?= $config_url; ?>" size="40" /></td>
            </tr>
            <tr>
              <td><?= $entry_ssl; ?></td>
              <td><input type="text" name="config_ssl" value="<?= $config_ssl; ?>" size="40" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?= $entry_name; ?></td>
              <td><input type="text" name="config_name" value="<?= $config_name; ?>" size="40" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?= $entry_owner; ?></td>
              <td><input type="text" name="config_owner" value="<?= $config_owner; ?>" size="40" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?= $entry_address; ?></td>
              <td><textarea name="config_address" cols="40" rows="5"><?= $config_address; ?></textarea></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?= $entry_email; ?></td>
              <td><input type="text" name="config_email" value="<?= $config_email; ?>" size="40" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?= $entry_telephone; ?></td>
              <td><input type="text" name="config_telephone" value="<?= $config_telephone; ?>" /></td>
            </tr>
            <tr>
              <td><?= $entry_fax; ?></td>
              <td><input type="text" name="config_fax" value="<?= $config_fax; ?>" /></td>
            </tr>
          </table>
        </div>
        <div id="tab-store">
          <table class="form">
            <tr>
              <td><span class="required">*</span> <?= $entry_title; ?></td>
              <td><input type="text" name="config_title" value="<?= $config_title; ?>" /></td>
            </tr>
            <tr>
              <td><?= $entry_meta_description; ?></td>
              <td><textarea name="config_meta_description" cols="40" rows="5"><?= $config_meta_description; ?></textarea></td>
            </tr>
            <tr>
              <td><?= $entry_template; ?></td>
              <td><select name="config_template" onchange="$('#template').load('index.php?route=setting/store/template&template=' + encodeURIComponent(this.value));">
                  <? foreach ($templates as $template) { ?>
                  <? if ($template == $config_template) { ?>
                  <option value="<?= $template; ?>" selected="selected"><?= $template; ?></option>
                  <? } else { ?>
                  <option value="<?= $template; ?>"><?= $template; ?></option>
                  <? } ?>
                  <? } ?>
                </select></td>
            </tr>
            <tr>
              <td></td>
              <td id="template"></td>
            </tr>
            <tr>
              <td><?= $entry_layout; ?></td>
              <td><select name="config_default_layout_id">
                  <? foreach ($layouts as $layout) { ?>
                  <? if ($layout['layout_id'] == $config_default_layout_id) { ?>
                  <option value="<?= $layout['layout_id']; ?>" selected="selected"><?= $layout['name']; ?></option>
                  <? } else { ?>
                  <option value="<?= $layout['layout_id']; ?>"><?= $layout['name']; ?></option>
                  <? } ?>
                  <? } ?>
                </select></td>
            </tr>
          </table>
        </div>
        <div id="tab-local">
          <table class="form">
            <tr>
              <td><?= $entry_country; ?></td>
              <td>
                 <?= $this->builder->set_config('country_id', 'name');?>
                 <?= $this->builder->build('select', $countries, "config_country_id", $config_country_id, array('class'=>"country_select"));?>
              </td>
            </tr>
            <tr>
              <td><?= $entry_zone; ?></td>
              <td><select name="config_zone_id" class="zone_select" zone_id="<?=$config_zone_id;?>"></select></td>
            </tr>
            <tr>
              <td><?= $entry_language; ?></td>
              <td><select name="config_language">
                  <? foreach ($languages as $language) { ?>
                  <? if ($language['code'] == $config_language) { ?>
                  <option value="<?= $language['code']; ?>" selected="selected"><?= $language['name']; ?></option>
                  <? } else { ?>
                  <option value="<?= $language['code']; ?>"><?= $language['name']; ?></option>
                  <? } ?>
                  <? } ?>
                </select></td>
            </tr>
            <tr>
              <td><?= $entry_currency; ?></td>
              <td><select name="config_currency">
                  <? foreach ($currencies as $currency) { ?>
                  <? if ($currency['code'] == $config_currency) { ?>
                  <option value="<?= $currency['code']; ?>" selected="selected"><?= $currency['title']; ?></option>
                  <? } else { ?>
                  <option value="<?= $currency['code']; ?>"><?= $currency['title']; ?></option>
                  <? } ?>
                  <? } ?>
                </select></td>
            </tr>
          </table>
        </div>
        <div id="tab-option">
          <table class="form">
            <tr>
              <td><span class="required">*</span> <?= $entry_catalog_limit; ?></td>
              <td><input type="text" name="config_catalog_limit" value="<?= $config_catalog_limit; ?>" size="3" /></td>
            </tr>
            <tr>
              <td><?= $entry_allowed_shipping_zone; ?></td>
              <td>
                 <? $this->builder->set_config('geo_zone_id','name');?>
                 <?=$this->builder->build('select',$geo_zones, "config_allowed_shipping_zone", (int)$config_allowed_shipping_zone);?>
              </td>
            </tr>
            <tr>
              <td><?= $entry_tax; ?></td>
              <td><? if ($config_show_price_with_tax) { ?>
                <input type="radio" name="config_show_price_with_tax" value="1" checked="checked" />
                <?= $text_yes; ?>
                <input type="radio" name="config_show_price_with_tax" value="0" />
                <?= $text_no; ?>
                <? } else { ?>
                <input type="radio" name="config_show_price_with_tax" value="1" />
                <?= $text_yes; ?>
                <input type="radio" name="config_show_price_with_tax" value="0" checked="checked" />
                <?= $text_no; ?>
                <? } ?></td>
            </tr>
            <tr>
              <td><?= $entry_tax_default; ?></td>
              <td><select name="config_tax_default">
                  <option value=""><?= $text_none; ?></option>
                  <?  if ($config_tax_default == 'shipping') { ?>
                  <option value="shipping" selected="selected"><?= $text_shipping; ?></option>
                  <? } else { ?>
                  <option value="shipping"><?= $text_shipping; ?></option>
                  <? } ?>
                  <?  if ($config_tax_default == 'payment') { ?>
                  <option value="payment" selected="selected"><?= $text_payment; ?></option>
                  <? } else { ?>
                  <option value="payment"><?= $text_payment; ?></option>
                  <? } ?>
                </select></td>
            </tr>
            <tr>
              <td><?= $entry_tax_customer; ?></td>
              <td><select name="config_tax_customer">
                  <option value=""><?= $text_none; ?></option>
                  <?  if ($config_tax_customer == 'shipping') { ?>
                  <option value="shipping" selected="selected"><?= $text_shipping; ?></option>
                  <? } else { ?>
                  <option value="shipping"><?= $text_shipping; ?></option>
                  <? } ?>
                  <?  if ($config_tax_customer == 'payment') { ?>
                  <option value="payment" selected="selected"><?= $text_payment; ?></option>
                  <? } else { ?>
                  <option value="payment"><?= $text_payment; ?></option>
                  <? } ?>
                </select></td>
            </tr>
            <tr>
              <td><?= $entry_customer_group; ?></td>
              <td><select name="config_customer_group_id">
                  <? foreach ($customer_groups as $customer_group) { ?>
                  <? if ($customer_group['customer_group_id'] == $config_customer_group_id) { ?>
                  <option value="<?= $customer_group['customer_group_id']; ?>" selected="selected"><?= $customer_group['name']; ?></option>
                  <? } else { ?>
                  <option value="<?= $customer_group['customer_group_id']; ?>"><?= $customer_group['name']; ?></option>
                  <? } ?>
                  <? } ?>
                </select></td>
            </tr>
            <tr>
              <td><?= $entry_customer_price; ?></td>
              <td><? if ($config_customer_price) { ?>
                <input type="radio" name="config_customer_price" value="1" checked="checked" />
                <?= $text_yes; ?>
                <input type="radio" name="config_customer_price" value="0" />
                <?= $text_no; ?>
                <? } else { ?>
                <input type="radio" name="config_customer_price" value="1" />
                <?= $text_yes; ?>
                <input type="radio" name="config_customer_price" value="0" checked="checked" />
                <?= $text_no; ?>
                <? } ?></td>
            </tr>
            <tr>
              <td><?= $entry_customer_approval; ?></td>
              <td><? if ($config_customer_approval) { ?>
                <input type="radio" name="config_customer_approval" value="1" checked="checked" />
                <?= $text_yes; ?>
                <input type="radio" name="config_customer_approval" value="0" />
                <?= $text_no; ?>
                <? } else { ?>
                <input type="radio" name="config_customer_approval" value="1" />
                <?= $text_yes; ?>
                <input type="radio" name="config_customer_approval" value="0" checked="checked" />
                <?= $text_no; ?>
                <? } ?></td>
            </tr>
            <tr>
              <td><?= $entry_guest_checkout; ?></td>
              <td><? if ($config_guest_checkout) { ?>
                <input type="radio" name="config_guest_checkout" value="1" checked="checked" />
                <?= $text_yes; ?>
                <input type="radio" name="config_guest_checkout" value="0" />
                <?= $text_no; ?>
                <? } else { ?>
                <input type="radio" name="config_guest_checkout" value="1" />
                <?= $text_yes; ?>
                <input type="radio" name="config_guest_checkout" value="0" checked="checked" />
                <?= $text_no; ?>
                <? } ?></td>
            </tr>
            <tr>
              <td><?= $entry_account; ?></td>
              <td><select name="config_account_id">
                  <option value="0"><?= $text_none; ?></option>
                  <? foreach ($informations as $information) { ?>
                  <? if ($information['information_id'] == $config_account_id) { ?>
                  <option value="<?= $information['information_id']; ?>" selected="selected"><?= $information['title']; ?></option>
                  <? } else { ?>
                  <option value="<?= $information['information_id']; ?>"><?= $information['title']; ?></option>
                  <? } ?>
                  <? } ?>
                </select></td>
            </tr>
            <tr>
              <td><?= $entry_checkout; ?></td>
              <td><select name="config_checkout_id">
                  <option value="0"><?= $text_none; ?></option>
                  <? foreach ($informations as $information) { ?>
                  <? if ($information['information_id'] == $config_checkout_id) { ?>
                  <option value="<?= $information['information_id']; ?>" selected="selected"><?= $information['title']; ?></option>
                  <? } else { ?>
                  <option value="<?= $information['information_id']; ?>"><?= $information['title']; ?></option>
                  <? } ?>
                  <? } ?>
                </select></td>
            </tr>
            <tr>
              <td><?= $entry_stock_display; ?></td>
              <td>
                <?= $this->builder->build('radio', $data_stock_display_types, "config_stock_display", $config_stock_display, array('class'=>'display_stock_radio'));?>
              </td>
            </tr>
            <tr>
              <td><?= $entry_stock_checkout; ?></td>
              <td><? if ($config_stock_checkout) { ?>
                <input type="radio" name="config_stock_checkout" value="1" checked="checked" />
                <?= $text_yes; ?>
                <input type="radio" name="config_stock_checkout" value="0" />
                <?= $text_no; ?>
                <? } else { ?>
                <input type="radio" name="config_stock_checkout" value="1" />
                <?= $text_yes; ?>
                <input type="radio" name="config_stock_checkout" value="0" checked="checked" />
                <?= $text_no; ?>
                <? } ?></td>
            </tr>
            <tr>
              <td><?= $entry_order_status; ?></td>
              <td><select name="config_order_status_id">
                  <? foreach ($order_statuses as $order_status) { ?>
                  <? if ($order_status['order_status_id'] == $config_order_status_id) { ?>
                  <option value="<?= $order_status['order_status_id']; ?>" selected="selected"><?= $order_status['name']; ?></option>
                  <? } else { ?>
                  <option value="<?= $order_status['order_status_id']; ?>"><?= $order_status['name']; ?></option>
                  <? } ?>
                  <? } ?>
                </select></td>
            </tr>
            <tr>
              <td><?= $entry_cart_weight; ?></td>
              <td><? if ($config_cart_weight) { ?>
                <input type="radio" name="config_cart_weight" value="1" checked="checked" />
                <?= $text_yes; ?>
                <input type="radio" name="config_cart_weight" value="0" />
                <?= $text_no; ?>
                <? } else { ?>
                <input type="radio" name="config_cart_weight" value="1" />
                <?= $text_yes; ?>
                <input type="radio" name="config_cart_weight" value="0" checked="checked" />
                <?= $text_no; ?>
                <? } ?></td>
            </tr>
          </table>
        </div>
        <div id="tab-image">
          <table class="form">
            <tr>
              <td><?= $entry_logo; ?></td>
              <td>
                 <div class="image"><img src="<?= $logo; ?>" alt="" id="thumb-logo" />
                 <input type="hidden" name="config_logo" value="<?= $config_logo; ?>" id="config_logo" />
                 <br />
                 <a onclick="el_uploadSingle('config_logo', 'thumb-logo');"><?= $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                 <a onclick="$('#thumb-logo').attr('src', '<?= $no_image; ?>'); $('#config_logo').attr('value', '');"><?= $text_clear; ?></a></div>
              </td>
            </tr>
            <tr>
              <td><?= $entry_icon; ?></td>
              <td>
                 <div class="image"><img src="<?= $icon; ?>" alt="" id="thumb-icon" />
                 <input type="hidden" name="config_icon" value="<?= $config_icon; ?>" id="config_icon" />
                 <br />
                 <a onclick="el_uploadSingle('config_icon', 'thumb-icon');"><?= $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                 <a onclick="$('#thumb-icon').attr('src', '<?= $no_image; ?>'); $('#config_icon').attr('value', '');"><?= $text_clear; ?></a></div>
              </td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?= $entry_image_category; ?></td>
              <td><input type="text" name="config_image_category_width" value="<?= $config_image_category_width; ?>" size="3" />
                x
                <input type="text" name="config_image_category_height" value="<?= $config_image_category_height; ?>" size="3" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?= $entry_image_thumb; ?></td>
              <td><input type="text" name="config_image_thumb_width" value="<?= $config_image_thumb_width; ?>" size="3" />
                x
                <input type="text" name="config_image_thumb_height" value="<?= $config_image_thumb_height; ?>" size="3" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?= $entry_image_popup; ?></td>
              <td><input type="text" name="config_image_popup_width" value="<?= $config_image_popup_width; ?>" size="3" />
                x
                <input type="text" name="config_image_popup_height" value="<?= $config_image_popup_height; ?>" size="3" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?= $entry_image_product; ?></td>
              <td><input type="text" name="config_image_product_width" value="<?= $config_image_product_width; ?>" size="3" />
                x
                <input type="text" name="config_image_product_height" value="<?= $config_image_product_height; ?>" size="3" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?= $entry_image_additional; ?></td>
              <td><input type="text" name="config_image_additional_width" value="<?= $config_image_additional_width; ?>" size="3" />
                x
                <input type="text" name="config_image_additional_height" value="<?= $config_image_additional_height; ?>" size="3" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?= $entry_image_related; ?></td>
              <td><input type="text" name="config_image_related_width" value="<?= $config_image_related_width; ?>" size="3" />
                x
                <input type="text" name="config_image_related_height" value="<?= $config_image_related_height; ?>" size="3" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?= $entry_image_compare; ?></td>
              <td><input type="text" name="config_image_compare_width" value="<?= $config_image_compare_width; ?>" size="3" />
                x
                <input type="text" name="config_image_compare_height" value="<?= $config_image_compare_height; ?>" size="3" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?= $entry_image_wishlist; ?></td>
              <td><input type="text" name="config_image_wishlist_width" value="<?= $config_image_wishlist_width; ?>" size="3" />
                x
                <input type="text" name="config_image_wishlist_height" value="<?= $config_image_wishlist_height; ?>" size="3" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?= $entry_image_cart; ?></td>
              <td><input type="text" name="config_image_cart_width" value="<?= $config_image_cart_width; ?>" size="3" />
                x
                <input type="text" name="config_image_cart_height" value="<?= $config_image_cart_height; ?>" size="3" /></td>
            </tr>
          </table>
        </div>
        <div id="tab-server">
          <table class="form">
            <tr>
              <td><?= $entry_use_ssl; ?></td>
              <td><? if ($config_use_ssl) { ?>
                <input type="radio" name="config_use_ssl" value="1" checked="checked" />
                <?= $text_yes; ?>
                <input type="radio" name="config_use_ssl" value="0" />
                <?= $text_no; ?>
                <? } else { ?>
                <input type="radio" name="config_use_ssl" value="1" />
                <?= $text_yes; ?>
                <input type="radio" name="config_use_ssl" value="0" checked="checked" />
                <?= $text_no; ?>
                <? } ?></td>
            </tr>
          </table>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">//<!--
$('#template').load('index.php?route=setting/store/template&template=' + encodeURIComponent($('select[name=\'config_template\']').attr('value')));
//--></script> 

<?= $this->builder->js('load_zones', 'table.form', '.country_select', '.zone_select');?>

<script type="text/javascript">//<!--
$('#tabs a').tabs();
//--></script> 
<?=$this->builder->js('errors',$errors);?>
<?= $footer; ?>