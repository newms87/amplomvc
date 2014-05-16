<?php
class Catalog_Controller_Block_Cart_Coupon extends Controller
{
	public function build($settings)
	{
		$settings['action'] = site_url('block/cart/coupon/apply_coupon');

		$this->response->setOutput($this->render('block/cart/coupon', $settings));
	}

	//TODO: Handel SESSION coupons (probably from cart?) sitewide...
	public function apply_coupon()
	{
		//TODO: This should be moved to $this->cart->getCoupon()
		$coupon_info = $this->System_Model_Coupon->getCoupon($_POST['coupon_code']);

		if (!$coupon_info) {
			$this->message->add('error', $this->System_Model_Coupon->getError());
		} else {
			$_SESSION['coupons'][$_POST['coupon_code']] = $coupon_info;

			$this->message->add('success', _l("Your coupon has been applied!"));
		}

		if ($this->request->isAjax()) {
			$this->response->setOutput($this->message->toJSON());
		} else {
			if ($this->request->hasRedirect()) {
				$this->request->doRedirect();
			} else {
				redirect('cart/cart');
			}
		}
	}
}
