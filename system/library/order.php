<?php
class Order Extends Library
{
	private $error = array();
	
	public function __construct($registry)
  	{
  		parent::__construct($registry);

		$this->language->system('order');
	}
	
	public function _e($code, $key)
	{
		$this->error[$code] = $this->language->get($key);
	}
	
	public function getErrors($pop = false, $name_format = false)
	{
		if ($pop) {
			$this->error = array();
		}
		
		if ($name_format) {
			return $this->tool->name_format($name_format, $this->error);
		}
		
		return $this->error;
	}
	
	public function hasError($type)
	{
		return !empty($this->error);
	}
	
	public function add()
	{
		if (!$this->validate()) {
			return false;
		}
		
		$data = array();
		
		//Validate Shipping Address & Method
		if ($this->cart->hasShipping()) {
			if (!$this->cart->hasShippingAddress()) {
				$this->_e('O-1', 'error_shipping_address');
				return false;
			}
			
			if (!$this->cart->hasShippingMethod()) {
				$this->_e('O-2', 'error_shipping_method');
			}
		}
		
		//Validate Payment Address & Method
		if (!$this->cart->hasPaymentAddress()) {
			$this->_e('O-3', 'error_payment_address');
			return false;
		}
		
		if (!$this->cart->hasPaymentMethod()) {
			$this->_e('O-4', 'error_payment_method');
			return false;
		}
		
		//Customer Checkout
		if ($this->customer->isLogged()) {
			$data = $this->customer->info();
		}
		elseif ($this->config->get('config_guest_checkout')) {
			$data['customer_id'] = 0;
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}
		//Guest checkout not allowed and customer not logged in
		else {
			$this->_e('O-5', 'error_checkout_guest');
			return false;
		}
		
		//Order Information
		$data['invoice_id'] = $this->System_Model_Order->generateInvoiceId();
		$data['store_id'] = $this->config->get('config_store_id');
		$data['language_id'] = $this->config->get('config_language_id');
		$data['currency_id'] = $this->currency->getId();
		$data['currency_value'] = $this->currency->getValue();
		
		//Payment info
		$payment_address = $this->getPaymentAddress();
		
		foreach ($payment_address as $key => $value) {
			$data['payment_' . $key] = $value;
		}
		
		$data['payment_method'] = $this->cart->getPaymentMethod();
		
		//Shipping info
		if ($this->cart->hasShipping()) {
			$shipping_address = $this->getShippingAddress();
			
			foreach ($shipping_address as $key => $value) {
				$data['shipping_' . $key] = $value;
			}
			
			$data['shipping_method'] = $this->getShippingMethod();
		}
		
		//Totals
		$totals = $this->getTotals();
		
		$data['total'] = $totals['total'];
		$data['totals'] = $totals['data'];
		
		//Products
		$products = $this->getProducts();
		
		foreach ($products as &$product) {
			$product['tax'] = $this->tax->getTax($product['total'], $product['tax_class_id']);
		} unset($product);
		
		$data['products'] = $products;
		
		// Gift Voucher
		if ($this->cart->hasVouchers()) {
			$data['vouchers'] = $this->cart->getVouchers();
		}
		
		//Comments
		$data['comment'] = $this->cart->getComment();
		
		//TODO: We should track affiliates via the session, not Cookies!
		// (Eg: $this->affiliate->isTracking(); )
		$data['affiliate_id'] = 0;
		$data['commission'] = 0;
		
		if (isset($_COOKIE['tracking'])) {
			$affiliate_info = $this->Model_Affiliate_Affiliate->getAffiliateByCode($_COOKIE['tracking']);
			
			if ($affiliate_info) {
				$data['affiliate_id'] = $affiliate_info['affiliate_id'];
				$data['commission'] = ($total / 100) * $affiliate_info['commission'];
			}
		}
		
		//Client Location / Browser Info
		$data['ip'] = $_SERVER['REMOTE_ADDR'];
		
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$data['forwarded_ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$data['forwarded_ip'] = $_SERVER['HTTP_CLIENT_IP'];
		}
		
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		}
		
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$data['accept_language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		}
		
		$order_id = $this->System_Model_Order->addOrder($data);
		
		$this->session->data['order_id'] = $order_id;
		
		return $order_id;
	}
	
	public function hasOrder()
	{
		return !empty($this->session->data['order_id']);
	}
	
	public function getId()
	{
		return !empty($this->session->data['order_id']) ? $this->session->data['order_id'] : false;
	}
	
	public function get($order_id = null)
	{
		$order_id = $order_id ? $order_id : $this->getId();
		
		if (!$order_id) {
			return null;
		}
		
		return $this->System_Model_Order->getOrder($order_id);
	}
	
	public function synchronizeOrders($customer)
	{
		if (empty($customer) || empty($customer['customer_id']) || empty($customer['email'])) {
			return;
		}
		
		$this->db->query(
			"UPDATE " . DB_PREFIX . "order SET customer_id = '" . (int)$customer['customer_id'] . "'" .
			" WHERE customer_id = 0 AND email = '" . $this->db->escape($customer['email']) . "'"
		);
	}
	
	public function update($order_id, $order_status_id, $comment = '', $notify = false)
	{
		$order = $this->get($order_id);
		
		//order does not exist or has already been processed
		if (!$order || $order['order_status_id'] === $order_status_id) {
			return false;
		}
		
		// Fraud Detection
		if ($this->config->get('config_fraud_detection') && $this->fraud->atRisk($order)) {
			$order_status_id = $this->config->get('config_fraud_status_id');
		}

		// Blacklist
		if ($order['customer_id'] && $this->customer->isBlacklisted($order['customer_id'], array($order['ip']))) {
			$order_status_id = $this->config->get('config_order_status_id');
		}
		
		$this->System_Model_Order->updateOrderStatus($order_id, $order_status_id, $comment, $notify);
		
		if ($notify) {
			$this->mail->init();
			
			$this->mail->setTemplate('order_update_notify', $order);
			
			$this->mail->send();
		}
	}
	
	public function confirm($order)
	{
		$order_product_query = $this->query("SELECT op.*, p.manufacturer_id, p.cost FROM " . DB_PREFIX . "order_product op LEFT JOIN " . DB_PREFIX . "product p ON (op.product_id=p.product_id) WHERE op.order_id = '" . (int)$order_id . "'");
		
		// Products
		$order['order_products'] = array();
		
		foreach ($order_product_query->rows as $product) {
			//subtract Quantity from this product
			$this->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$product['quantity'] . ") WHERE product_id = '" . (int)$product['product_id'] . "' AND subtract = '1'");
			
			//Subtract Quantities from Product Option Values and Restrictions
			$option_value_query = $this->query("SELECT pov.product_option_value_id, pov.option_value_id FROM " . DB_PREFIX . "order_option oo LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (pov.product_option_value_id=oo.product_option_value_id) WHERE oo.order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");
			
			$pov_to_ov = array();
			
			foreach ($option_value_query->rows as $option_value) {
				$pov_to_ov[$option_value['product_option_value_id']] = $option_value['option_value_id'];
			}
			
			$order_options = $this->getOrderProductOptions($order_id, $product['order_product_id']);
			
			foreach ($order_options as $option) {
				$this->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
					
				$this->query("UPDATE " . DB_PREFIX . "product_option_value_restriction SET quantity = (quantity - " . (int) $product['quantity'] . ")" .
					" WHERE option_value_id = '" . ($pov_to_ov[$option['product_option_value_id']]) . "' AND restrict_option_value_id IN (" . implode(',', $pov_to_ov) . ")");
			}
			
			//Add Product Options to product data
			$product['option'] = $order_options;
			
			$order['order_products'][] = $product;
		}
		
		$this->cache->delete('product');
		
		// Downloads
		$order['order_downloads'] = $this->queryRows("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");
		
		// Gift Voucher
		$order_vouchers = $this->queryRows("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
		
		foreach ($order_vouchers as $order_voucher) {
			$voucher_id = $this->sendOrderVouchers($order_id);
		}
		
		$order['order_vouchers'] = $order_voucher_query->rows;
		
		// Send out any gift voucher mails
		if (!empty($order['order_vouchers']) && $this->config->get('config_complete_status_id') == $order_status_id) {
			$this->Model_Cart_Voucher->confirm($order_id);
		}
		
		// Order Totals
		$order_total_query = $this->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");
		
		foreach ($order_total_query->rows as $order_total) {
			$classname = 'Model_Total_' . $this->tool->format_classname($order_total['code']);
			
			if (method_exists($this->$classname, 'confirm')) {
				$this->$classname->confirm($order, $order_total);
			}
		}
		
		$order['order_totals'] = $order_total_query->rows;
		
		
		//Order Status
		$order_status_query = $this->db->query("SELECT name FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order['language_id'] . "'");
		
		$order['order_status'] = $order_status_query->num_rows ? $order_status_query->row['name'] : '';
		
		//Comments
		$order['notify_comment'] = ($comment && $notify) ? nl2br($comment) : '';
		
		//TODO: we can do better than this!
		$this->callController('mail/order', $order);
		
		//Generate and Email Excel Docs
		$product_ids = array();
		foreach ($order_product_query->rows as $p) {
			$product_ids[] = $p['product_id'];
		}
		
		$vendors = $this->query("SELECT p.manufacturer_id, m.vendor_id, m.name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id=m.manufacturer_id) WHERE p.product_id IN (" . implode(',',$product_ids) . ") GROUP BY m.vendor_id");
		
		foreach ($vendors->rows as $v) {
			$query = $this->query("SELECT c.*, ud.user_id FROM " . DB_PREFIX . "type_to_contact t2c JOIN " . DB_PREFIX . "contact c ON (t2c.contact_id=c.contact_id) ".
											"JOIN " . DB_PREFIX . "user_designer ud ON (t2c.type_id=ud.user_id AND t2c.type='user' AND ud.designer_id='$v[manufacturer_id]') WHERE c.email != '' AND c.email IS NOT NULL ORDER BY FIELD(c.contact_type,'shipping','primary','finance','customer_service')");
			
			$this->mail->init();
			
			$this->mail->setFrom($this->config->get('config_email'));
			$this->mail->setSender($order['store_name']);
			$email_list = array();
			if ($query->num_rows) {
				foreach ($query->rows as $r) {
					if(!isset($email_list[$r['user_id']]))
						$email_list[$r['user_id']] = $r;
				}
				$v['contact'] = $query->row;
			}
			else {
				$v['contact'] = array('street_1' => $this->config->get('config_address'));
			}
			
			$files = $this->generate_excel_order_invoice($v, $order);
			$this->mail->addAttachment($files);
			
			if (!$email_list) {
				$email_list[] = array(
					'first_name' => $this->config->get('config_name'),
					'last_name' => '',
					'email' => $this->config->get('config_email'),
					'company' => $this->config->get('config_title')
				);
			}
			
			foreach ($email_list as $e) {
				$insertables = array(
					'first_name'=> $e['first_name'],
					'last_name' => $e['last_name'],
					'email'	=> $e['email'],
					'company'	=> $e['company']
				);
				
				$subject = $this->tool->insertables($insertables, $this->config->get('mail_designer_invoice_subject'));
				$message = $this->tool->insertables($insertables, $this->config->get('mail_designer_invoice_message'));
				
				if ($this->config->get('config_debug_send_emails') && $e['email']) {
					$this->mail->setTo($e['email']);
					$this->mail->setCc($this->config->get('config_email'));
				}
				else {
					$this->mail->setTo($this->config->get('config_email'));
				}
				
				$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$this->mail->setHtml(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$this->mail->send();
			}
		}
	}

	public function generate_excel_order_invoice($vendor, $order)
	{
		_require(DIR_SYSTEM . 'php-excel/classes/PHPExcel/IOFactory.php');
		
		if (!is_dir(DIR_EXCEL_FPO . $vendor['vendor_id'])) {
			$mode = octdec($this->config->get('config_default_dir_mode'));
			mkdir(DIR_EXCEL_FPO . $vendor['vendor_id'], $mode, true);
			chmod(DIR_EXCEL_FPO . $vendor['vendor_id'], $mode);
		}
		
		$invoice_id = $order['invoice_prefix'] . $order['invoice_no'] . $order['order_id'];
		
		
		$total_lines = count($order['order_products']);
		
		if ($total_lines < 21) {
			$template = 'fpo.xls';
			$max_row = 37;
		}
		elseif ($total_lines <= 50) {
			$template = 'fpo_50.xls';
			$max_row = 67;
		}
		elseif ($total_lines <= 100) {
			$template = 'fpo_100.xls';
			$max_row = 116;
		}
		else {
			$template = 'fpo_100.xls';
			trigger_error("Need Larger FPO Template! Max Lines: $total_lines");
		}
		
		//GENERATE THE FPO
		$fpo_id = preg_replace('/INV/','FPO',$invoice_id);
		$fpo = DIR_EXCEL_TEMPLATE . $template;
		if (!file_exists($fpo)) {
			$msg ="Template not found: $fpo! Unable to generate FPO!";
			$this->message->add('warning', $msg);
			trigger_error($msg);
		}
		else {
			$e = PHPExcel_IOFactory::load($fpo);
			
			$e->setActiveSheetIndex(0);
			$sheet = $e->getActiveSheet();
			
			$sheet->setCellValue('E4',$fpo_id);
			$sheet->setCellValue('E5',date_format(new DateTime(),'M d, Y'));
			$sheet->setCellValue('E6',$vendor['vendor_id']);
			
			//VENDOR INFORMATION
			$row = 9;
			$col = ord('B');
			$sheet->setCellValue(chr($col).$row++,$vendor['name']);
			$sheet->setCellValue(chr($col).$row++,$vendor['contact']['street_1']);
			if(!empty($vendor['contact']['street_2']))
				$sheet->setCellValue(chr($col).$row++,$vendor['contact']['street_2']);
			if (isset($vendor['contact']['city'])) {
				$sheet->setCellValue(chr($col).$row++, $vendor['contact']['city'] . ', ' . $this->Model_Localisation_Zone->getZoneName($vendor['contact']['zone_id']) . ' ' . $vendor['contact']['postcode']);
				$sheet->setCellValue(chr($col).$row++, $this->Model_Localisation_Country->getCountryName($vendor['contact']['country_id']));
			
				$sheet->setCellValue(chr($col).$row++, $vendor['contact']['email']);
			}
			
			//CUSTOMER SHIPPING INFORMATION
			$row = 9;
			$col = ord('E');
			$sheet->setCellValue(chr($col).$row++,$order['shipping_firstname'] . " " . $order['shipping_lastname']);
			if(!empty($order['shipping_company']))
				$sheet->setCellValue(chr($col).$row++,$order['shipping_company']);
			$sheet->setCellValue(chr($col).$row++,$order['shipping_address_1']);
			if(!empty($order['shipping_address_2']))
				$sheet->setCellValue(chr($col).$row++,$order['shipping_address_2']);
			$sheet->setCellValue(chr($col).$row++, $order['shipping_city'] . ', ' . $order['shipping_zone'] . ' ' . $order['shipping_postcode']);
			$sheet->setCellValue(chr($col).$row++, $order['shipping_country']);
			
			$sheet->setCellValue('A17',$order['shipping_method']);
			
			$row = 20;
			foreach ($order['order_products'] as $p) {
				if((int)$p['manufacturer_id'] != (int)$vendor['manufacturer_id'])continue;
				
				$sheet->setCellValue('A'.$row,$p['name']);
				$sheet->setCellValue('B'.$row,$p['model']);
				$description = '';
				foreach($p['option'] as $o)
					$description .= html_entity_decode($o['name']) . ": " . html_entity_decode($o['value']);
				$sheet->setCellValue('C'.$row,$description);
				$sheet->setCellValue('E'.$row,$p['quantity']);
				$sheet->setCellValue('F'.$row,$p['cost']);
				$sheet->getRowDimension($row)->setRowHeight(-1);
				$row++;
				
				if($row > $max_row)
					break;
			}
			
			$fpo = DIR_EXCEL_FPO . $vendor['vendor_id'] . '/FinalPO_BettyConfidential_' . $vendor['name'] .'_' . $fpo_id . '.xls';
			$objWriter = PHPExcel_IOFactory::createWriter($e, 'Excel5');
			$objWriter->save($fpo);
			
			$this->query("UPDATE " . DB_PREFIX . "order SET fpo = '" . $this->db->escape($fpo) . "' WHERE order_id = '$order[order_id]'");
		}
		
		uasort($order['order_products'], function ($a,$b) { return $a['manufacturer_id'] > $b['manufacturer_id']; });
		
		$tax = null;
		$shipping = null;
		$coupon = array();
		foreach ($order['order_totals'] as $t) {
			if($t['code'] == 'shipping')
				$shipping = $t['value'];
			elseif($t['code'] == 'tax')
				$tax = $t;
			elseif($t['code'] == 'coupon')
				$coupon[] = $t;
		}
		
		
		$m_count = array();
		foreach ($order['order_products'] as $p) {
			$m_count[$p['manufacturer_id']] = 1;
		}
		
		$total_lines = (count($m_count)*3) + count($order['order_products']) + count($coupon);
		
		if ($total_lines < 21) {
			$template = 'packing_slip.xls';
			$max_row = 37;
			$ship_line = 39;
			$tax_line = 40;
		}
		elseif ($total_lines <= 50) {
			$template = 'packing_slip_50.xls';
			$max_row = 67;
			$ship_line = 69;
			$tax_line = 70;
		}
		elseif ($total_lines <= 100) {
			$template = 'packing_slip_100.xls';
			$max_row = 116;
			$ship_line = 118;
			$tax_line = 119;
		}
		else {
			$template = 'packing_slip_100.xls';
			trigger_error("Need Larger Packing Slip Template!");
		}
		
		//GENERATE THE PACKING SLIP
		$packslip = DIR_EXCEL_TEMPLATE . $template;
		if (!file_exists($packslip)) {
			$msg = "Template not found: $packslip! Unable to generate Packing Slip!";
			$this->message->add('warning', $msg);
			trigger_error("Template not found: $packslip! Unable to generate Packing Slip!");
		}
		else {
			$e = PHPExcel_IOFactory::load($packslip);
			
			$e->setActiveSheetIndex(0);
			$sheet = $e->getActiveSheet();
			
			$sheet->setCellValue('E4',$invoice_id);
			$sheet->setCellValue('E5',date_format(new DateTime(),'M d, Y'));
			$sheet->setCellValue('E6',$order['customer_id']);
			
			$row = 9;
			$col = ord('B');
			$sheet->setCellValue(chr($col).$row++,$order['shipping_firstname'] . " " . $order['shipping_lastname']);
			if(!empty($order['shipping_company']))
				$sheet->setCellValue(chr($col).$row++,$order['shipping_company']);
			$sheet->setCellValue(chr($col).$row++,$order['shipping_address_1']);
			if(!empty($order['shipping_address_2']))
				$sheet->setCellValue(chr($col).$row++,$order['shipping_address_2']);
			$sheet->setCellValue(chr($col).$row++, $order['shipping_city'] . ', ' . $order['shipping_zone'] . ' ' . $order['shipping_postcode']);
			$sheet->setCellValue(chr($col).$row++, $order['shipping_country']);
			$sheet->setCellValue(chr($col).$row++, $order['email']);
			
			$row = 9;
			$col = ord('E');
			$sheet->setCellValue(chr($col).$row++,$order['payment_firstname'] . " " . $order['payment_lastname']);
			if(!empty($order['payment_company']))
				$sheet->setCellValue(chr($col).$row++,$order['payment_company']);
			$sheet->setCellValue(chr($col).$row++,$order['payment_address_1']);
			if(!empty($order['payment_address_2']))
				$sheet->setCellValue(chr($col).$row++,$order['payment_address_2']);
			$sheet->setCellValue(chr($col).$row++, $order['payment_city'] . ', ' . $order['payment_zone'] . ' ' . $order['payment_postcode']);
			$sheet->setCellValue(chr($col).$row++, $order['payment_country']);
			$sheet->setCellValue(chr($col).$row++, $order['email']);
			
			
			$ship_from_style = array('font'=>array('bold'=>true,'size'=>'13'),
											'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
											);
			
			$manufacturers = $this->Model_Catalog_Manufacturer->getManufacturers();
			
			$row = 17;
			$curr_man = '';
			foreach ($order['order_products'] as $p) {
				if ($curr_man != $p['manufacturer_id']) {
					if ($row != 17) {
						$sheet->getRowDimension($row)->setRowHeight(30);
						$row++;
					}
					$name = '';
					foreach($manufacturers as $m)
						if($m['manufacturer_id'] == $p['manufacturer_id'])
							$name = $m['name'];
					$sheet->setCellValue('C'.$row,"The Following Products Ship From $name");
					$sheet->getStyle('C'.$row)->applyFromArray($ship_from_style);
					$sheet->getRowDimension($row)->setRowHeight(40);
					$curr_man = $p['manufacturer_id'];
					$row++;
				}
				$sheet->setCellValue('A'.$row,$p['name']);
				$sheet->setCellValue('B'.$row,$p['model']);
				$description = '';
				foreach($p['option'] as $o)
					$description .= html_entity_decode($o['name']) . ": " . html_entity_decode($o['value']);
				$sheet->setCellValue('C'.$row,$description);
				$sheet->setCellValue('E'.$row,$p['quantity']);
				$sheet->setCellValue('F'.$row,$p['price']);
				$sheet->getRowDimension($row)->setRowHeight(-1);
				$row++;
				if($row > $max_row)
					break;
			}
			
			if ($coupon && $row<=$max_row) {
				foreach ($coupon as $c) {
					$sheet->setCellValue('A'.$row,$c['title']);
					$sheet->setCellValue('B'.$row,'coupon');
					$sheet->setCellValue('E'.$row,1);
					$sheet->setCellValue('F'.$row,$c['value']);
				}
			}
			
			if ($shipping) {
				$sheet->setCellValue('G'.$ship_line,$shipping);
			}
			
			if ($tax) {
				$sheet->setCellValue('F'.$tax_line,$tax['title']);
				$sheet->setCellValue('G'.$tax_line,$tax['value']);
			}
			
			$packslip = DIR_EXCEL_FPO .$vendor['vendor_id'].'/PackingSlip_BettyConfidential_' . $vendor['name'] .'_' . $invoice_id . '.xls';
			$objWriter = PHPExcel_IOFactory::createWriter($e, 'Excel5');
			$objWriter->save($packslip);
		}
		
		return array($fpo,$packslip);
	}

	public function clear()
	{
		unset($this->session->data['order_id']);
	}
}