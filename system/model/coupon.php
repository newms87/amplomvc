<?php

class System_Model_Coupon extends Model
{
	public function getCoupon($code)
	{
		$code = $this->escape(strtolower($code));

		$coupon = $this->queryRow("SELECT * FROM " . DB_PREFIX . "coupon WHERE LCASE(code) = '$code' AND (date_start <= NOW() AND (date_end = '0000-00-00' OR date_end >= NOW())) AND status = '1' LIMIT 1");

		if (!$coupon) {
			$this->error['coupon_code'] = _l("Coupon is either invalid or expired!");
			return false;
		}

		if ($coupon['total'] > $this->cart->getSubTotal()) {
			$this->error['coupon_total'] = _l("You must have at least %s in your cart for this coupon.", $this->currency->format($coupon['total']));
			return false;
		}

		if ($coupon['uses_total'] > 0) {
			$use_count = $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "coupon_history WHERE coupon_id = " . (int)$coupon['coupon_id']);

			if ($use_count >= $coupon['uses_total']) {
				$this->error['coupon_uses'] = _l("This coupon has reached its usage limit.");
				return false;
			}
		}

		if (($coupon['uses_customer'] > 0 || $coupon['logged']) && !$this->customer->isLogged()) {
			$this->error['coupon_logged'] = _l("You must be logged in to use this coupon");
			return false;
		}

		if ($coupon['uses_customer'] > 0) {
			$customer_uses = $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "coupon_history WHERE coupon_id = " . (int)$coupon['coupon_id'] . " AND customer_id = " . (int)$this->customer->getId());

			if ($customer_uses >= $coupon['uses_customer']) {
				$this->error['coupon_customer_uses'] = _l("You have reached the usage limit for this coupon.");
				return false;
			}
		}

		$product_ids = $this->queryColumn("SELECT product_id FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = " . (int)$coupon['coupon_id']);

		if ($product_ids) {
			$has_product = false;

			foreach ($this->cart->getProductIds() as $product) {
				if (in_array($product['product_id'], $product_ids)) {
					$has_product = true;
					break;
				}
			}

			if (!$has_product) {
				$this->error['coupon_product'] = _l("You do not have the required products in your cart to use this coupon.");
				return false;
			}
		}

		$coupon['product'] = $product_ids;

		return $coupon;
	}

	public function loadAutoCoupons()
	{
		$customer_id = $this->customer->getId();

		$query = $this->query("SELECT * FROM " . DB_PREFIX . "coupon_customer WHERE customer_id = '" . (int)$customer_id . "'");

		if ($query->num_rows) {
			if (!$this->session->has('coupons')) {
				$this->session->set('coupons', array());
			}

			foreach ($query->rows as $cc) {
				$coupon = $this->getCoupon((int)$cc['coupon_id']);
				if ($coupon) {
					$this->session->get('coupons')[$coupon['code']] = $coupon;
				}
			}
		}
	}

	public function redeem($coupon_id, $order_id, $customer_id, $amount)
	{
		$redeem = array(
			'coupon_id'   => $coupon_id,
			'order_id'    => $order_id,
			'customer_id' => $customer_id,
			'amount'      => $amount,
			'date_added'  => $this->date->now(),
		);

		$this->insert('coupon_history', $redeem);
	}
}
