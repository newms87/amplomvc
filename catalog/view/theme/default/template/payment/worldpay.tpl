<form action="<?= $action; ?>" method="post">
  <input type="hidden" name="instId" value="<?= $merchant; ?>" />
  <input type="hidden" name="cartId" value="<?= $order_id; ?>" />
  <input type="hidden" name="amount" value="<?= $amount; ?>" />
  <input type="hidden" name="currency" value="<?= $currency; ?>" />
  <input type="hidden" name="desc" value="<?= $description; ?>" />
  <input type="hidden" name="name" value="<?= $name; ?>" />
  <input type="hidden" name="address" value="<?= $address; ?>" />
  <input type="hidden" name="postcode" value="<?= $postcode; ?>" />
  <input type="hidden" name="country" value="<?= $country; ?>" />
  <input type="hidden" name="tel" value="<?= $telephone; ?>" />
  <input type="hidden" name="email" value="<?= $email; ?>" />
  <input type="hidden" name="testMode" value="<?= $test; ?>" />
  <div class="buttons">
    <div class="right">
      <input type="submit" value="<?= $button_confirm; ?>" class="button" />
    </div>
  </div>
</form>
