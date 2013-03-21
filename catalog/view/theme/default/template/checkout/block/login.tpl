<div class="left">
  <div class='janrain_login'><?=$rpx_login;?></div>
  <h2><?= $text_new_customer; ?></h2>
  <p><?= $text_checkout; ?></p>
  <label for="register">
    <input type="radio" name="account" value="register" id="register" <?= $account != 'guest' ? 'checked="checked"' : '';?> />
    <b><?= $text_register; ?></b>
  </label>
  <br />
  <? if ($guest_checkout) { ?>
  <label for="guest">
    <input type="radio" name="account" value="guest" id="guest" <?= $account == 'guest' ? 'checked="checked"' : '';?> />
    <b><?= $text_guest; ?></b>
  </label>
  <br />
  <? } ?>
  <br />
  <p><?= $text_register_account; ?></p>
  <input type="button" value="<?= $button_continue; ?>" id="button-account" class="button" onclick="submit_checkout_item($(this));" />
  <br />
  <br />
</div>
<div id="login" class="right">
  <h2><?= $text_returning_customer; ?></h2>
  <p><?= $text_i_am_returning_customer; ?></p>
  <?= $form_login;?>
</div>