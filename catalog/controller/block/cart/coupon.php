<?php
class Catalog_Controller_Block_Cart_Coupon extends Controller
{
	public function index($settings)
	{
		$ajax = isset($settings['ajax']) ? $settings['ajax'] : false;

		if (isset($_POST['coupon_code'])) {
			$this->apply_coupon();
		}

		$data['ajax'] = $ajax;

		if ($ajax) {
			$data['ajax_url'] = $this->url->link('block/cart/coupon/ajax_apply_coupon');
		}

		$this->response->setOutput($this->render('block/cart/coupon', $data));
	}

	//TODO: Handel SESSION coupons (probably from cart?) sitewide...
	public function apply_coupon()
	{
		//TODO: This should be moved to $this->cart->getCoupon()
		$coupon_info = $this->System_Model_Coupon->getCoupon($_POST['coupon_code']);

		if (!$coupon_info) {
			$this->message->add('warning', _l("Warning: Coupon is either invalid, expired or reached it's usage limit!"));
		} else {
			$this->session->get('coupons')[$_POST['coupon_code']] = $coupon_info;

			$this->message->add('success', _l("Success: Your coupon discount has been applied!"));
		}
	}

	public function ajax_apply_coupon()
	{
		//TODO: This should be moved to $this->cart->getCoupon()
		$coupon_info = $this->System_Model_Coupon->getCoupon($_POST['coupon_code']);

		if (!$coupon_info) {
			$json['error'] = _l("Warning: Coupon is either invalid, expired or reached it's usage limit!");
		} else {
			$this->session->get('coupons')[$_POST['coupon_code']] = $coupon_info;

			$json['success'] = _l("Success: Your coupon discount has been applied!");
		}

		$this->response->setOutput(json_encode($json));
	}
}
