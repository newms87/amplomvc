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

	public function apply_coupon()
	{
		$this->language->load('cart/cart');

		$coupon_info = $this->Model_Cart_Coupon->getCoupon($_POST['coupon_code']);

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

		$coupon_info = $this->Model_Cart_Coupon->getCoupon($_POST['coupon_code']);

		if (!$coupon_info) {
			$json['error'] = $this->_('error_coupon');
		} else {
			$this->session->data['coupons'][$_POST['coupon_code']] = $coupon_info;

			$json['success'] = $this->_('text_coupon');
		}

		echo json_encode($json);
		exit;
	}
}