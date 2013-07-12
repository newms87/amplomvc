<?php
class System_Model_Voucher extends Model 
{
	public function addVoucher($order_id, $data)
	{
		$data['order_id'] = $order_id;
		$data['date_added'] = $this->date->now();
		
		$voucher_id = $this->insert('voucher', $data);
		
		return $voucher_id;
	}
	
	public function getVoucher($voucher_id)
	{
		$query = 
			"SELECT *, vt.name as theme FROM " . DB_PREFIX . "voucher v" .
			" LEFT JOIN " . DB_PREFIX . "voucher_theme vt ON (vt.voucher_theme_id=v.voucher_theme_id)" .
			" WHERE voucher_id = " . (int)$voucher_id;
		
		return $this->queryRow($query);
	}
	
	/**
	 * Retrieve an active voucher by the voucher code.
	 */
	public function getVoucherByCode($code)
	{
		$query =
			"SELECT * FROM " . DB_PREFIX . "voucher v" .
			" LEFT JOIN " . DB_PREFIX . "voucher_theme vt ON (vt.voucher_theme_id=v.voucher_theme_id)" .
			" LEFT JOIN " . DB_PREFIX . "order o ON (o.order_id=v.order_id)" .
			" WHERE v.code = '" . $this->db->escape($code) . "' AND v.status = 1" .
			" AND (v.order_id = 0 OR o.order_status_id = '" . (int)$this->config->get('config_complete_status_id') . "')";
		
		$voucher = $this->queryRow($query);
		
		if ($voucher && $voucher['order_id']) {
			$amounts = $this->queryColumn("SELECT amount FROM " . DB_PREFIX . "voucher_history WHERE voucher_id = " . (int)$voucher['voucher_id']);
			
			$voucher['used_amount'] = array_sum($amounts);
		}
		
		return $voucher;
	}
	
	public function confirmOrder($order_id)
	{
		$vouchers = $this->queryColumn("SELECT voucher_id FROM " . DB_PREFIX . "voucher WHERE order_id = '" . (int)$order_id . "'");
		
		if ($vouchers) {
			foreach ($vouchers as $voucher_id) {
				$this->activate($voucher_id);
			}
		}
	}
	
	public function activate($voucher_id)
	{
		$voucher = $this->queryRow("SELECT *, vt.name AS theme FROM " . DB_PREFIX . "voucher v LEFT JOIN " . DB_PREFIX . "voucher_theme vt ON (v.voucher_theme_id = vt.voucher_theme_id) WHERE v.voucher_id = '" . (int)$voucher_id . "'");
		
		if (!$voucher) {
			return false;
		}
		
		if ($voucher['order_id']) {
			$voucher['order'] = $this->order->get($voucher['order_id']);
		}
		
		$this->mail->init();
				
		$this->mail->setTemplate('mail/voucher', $voucher);
		
		$this->mail->send();
		
		return true;
	}
	
	public function redeem($voucher_id, $order_id, $amount)
	{
		$this->query("INSERT INTO `" . DB_PREFIX . "voucher_history` SET voucher_id = '" . (int)$voucher_id . "', order_id = '" . (int)$order_id . "', amount = '" . (float)$amount . "', date_added = NOW()");
	}
}
