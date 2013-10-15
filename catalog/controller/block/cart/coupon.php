<?php
class Catalog_Controller_Block_Cart_Coupon extends Controller
{
	public function index($settings)
	{
		$this->language->load('block/cart/coupon');
		$this->template->load('block/cart/coupon');

		$ajax = isset($settings['ajax']) ? $settings['ajax'] : false;

		if (isset($_POST['coupon_code'])) {
			$this->apply_coupon();
		}

		$this->data['ajax'] = $ajax;

		if ($ajax) {
			$this->data['ajax_url'] = $this->url->link('block/cart/coupon/ajax_apply_coupon');
		}

		$this->response->setOutput($this->render());
	}

	//TODO: Handel SESSION coupons (probably from cart?) sitewide...
	public function apply_coupon()
	{
		$this->language->load('cart/cart');

		//TODO: This should be moved to $this->cart->getCoupon()
		$coupon_info = $this->System_Model_Coupon->getCoupon($_POST['coupon_code']);

		if (!$coupon_info) {
			$this->message->add('warning', $this->_('error_coupon'));
		} else {
			$this->session->data['coupons'][$_POST['coupon_code']] = $coupon_info;

			$this->message->add('success', $this->_('text_coupon'));
		}
	}

	public function ajax_apply_coupon()
	{
		$this->language->load('block/cart/coupon');

		//TODO: This should be moved to $this->cart->getCoupon()
		$coupon_info = $this->System_Model_Coupon->getCoupon($_POST['coupon_code']);

		if (!$coupon_info) {
			$json['error'] = $this->_('error_coupon');
		} else {
			$this->session->data['coupons'][$_POST['coupon_code']] = $coupon_info;

			$json['success'] = $this->_('text_coupon');
		}

		$this->response->setOutput(json_encode($json));
	}
}
