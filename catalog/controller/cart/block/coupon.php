<?php
class Catalog_Controller_Cart_Block_Coupon extends Controller
{
	
	public function index($settings = null, $ajax = false)
	{
		$this->language->load('cart/block/coupon');
		
		$this->template->load('cart/block/coupon');

		if (isset($_POST['coupon_code'])) {
			$this->apply_coupon();
		}
		
		$this->data['ajax'] = $ajax;
		
		if ($ajax) {
			$this->data['ajax_url'] = $this->url->link('cart/block/coupon/ajax_apply_coupon');
		}
		
		$this->response->setOutput($this->render());
	}
	
	public function apply_coupon()
	{
		$this->language->load('cart/cart');
		
		$coupon_info = $this->Model_Cart_Coupon->getCoupon($_POST['coupon_code']);
		
		if (!$coupon_info) {
			$this->message->add('warning', $this->_('error_coupon'));
		}
		else {
			$this->session->data['coupons'][$_POST['coupon_code']] = $coupon_info;
			
			$this->message->add('success', $this->_('text_coupon'));
		}
	}
	
	public function ajax_apply_coupon()
	{
		$this->language->load('cart/block/coupon');
		
		$coupon_info = $this->Model_Cart_Coupon->getCoupon($_POST['coupon_code']);
		
		if (!$coupon_info) {
			$json['error'] = $this->_('error_coupon');
		}
		else {
			$this->session->data['coupons'][$_POST['coupon_code']] = $coupon_info;
			
			$json['success'] = $this->_('text_coupon');
		}
		
		echo json_encode($json);
		exit;
	}
}