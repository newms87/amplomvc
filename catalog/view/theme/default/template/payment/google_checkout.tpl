<div style="text-align: right; clear: both;">
  <form action="<?= $action; ?>" method="post" id="payment">
    <input type="hidden" name="cart" value="<?= $cart; ?>">
    <input type="hidden" name="signature" value="<?= $signature; ?>">
    <input type="image" name="Google Checkout" alt="Fast checkout through Google" src="<?= $button; ?>" height="46" width="180">
  </form>
</div>
