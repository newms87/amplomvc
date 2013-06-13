<?php
class Catalog_Model_Checkout_Order extends Model 
{
	public function addOrder($data)
	{
		//TODO Move this to plugin
		if (!isset($data['customer_id'])) {
			$data['customer_id'] = 0;
			$data['customer_group_id'] = 0;
			$data['firstname'] = 'guest';
			$data['lastname'] = 'guest';
			$data['email'] = 'guest';
		}
		
		//TODO - Remove invoice_no from DB?
		$data['invoice_no'] = 0;
		$data['date_added'] = $this->tool->format_datetime();
		$data['date_modified'] = $this->tool->format_datetime();
		
		$order_id = $this->insert('order', $data);
		
		foreach ($data['products'] as $product) {
			$product['order_id'] = $order_id;
			
			$order_product_id = $this->insert('order_product', $product);
 
			foreach ($product['option'] as $option) {
				$option['order_id'] = $order_id;
				$option['order_product_id'] = $order_product_id;
				$option['value'] = $option['option_value'];
				
				$this->insert('order_option', $option);
			}
				
			foreach ($product['download'] as $download) {
				$download['order_id'] = $order_id;
				$download['order_product_id'] = $order_product_id;
				$download['remaining'] = $download['remaining'] * $product['quantity'];
				
				$this->insert('order_download', $download);
			}
		}
		
		foreach ($data['vouchers'] as $voucher) {
			$voucher['order_id'] = $order_id;
			
			$this->insert('order_voucher', $voucher);
		}
		
		foreach ($data['totals'] as $total) {
			$total['order_id'] = $order_id;
			
			$this->insert('order_total', $total);
		}

		return $order_id;
	}

	public function getOrder($order_id)
	{
		$order_query = $this->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");
			
		if ($order_query->num_rows) {
			$country_query = $this->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");
			
			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}
			
			$zone_query = $this->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}
			
			$country_query = $this->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");
			
			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}
			
			$zone_query = $this->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$language_info = $this->Model_Localisation_Language->getLanguage($order_query->row['language_id']);
			
			if ($language_info) {
				$language_code = $language_info['code'];
				$language_filename = $language_info['filename'];
				$language_directory = $language_info['directory'];
			} else {
				$language_code = '';
				$language_filename = '';
				$language_directory = '';
			}
			
			$additional_info = array(
				'shipping_zone_code'		=> $shipping_zone_code,
				'shipping_iso_code_2'	=> $shipping_iso_code_2,
				'shipping_iso_code_3'	=> $shipping_iso_code_3,
				'payment_zone_code'		=> $payment_zone_code,
				'payment_iso_code_2'		=> $payment_iso_code_2,
				'payment_iso_code_3'		=> $payment_iso_code_3,
				'language_code'			=> $language_code,
				'language_filename'		=> $language_filename,
				'language_directory'		=> $language_directory
			);
			
			return array_merge($order_query->row, $additional_info);
			
		} else {
			return false;
		}
	}
	
	public function getOrderStatus($order_id)
	{
		$query = $this->query("SELECT o.order_status_id, os.name FROM " . DB_PREFIX . "order o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id=os.order_status_id) WHERE o.order_id = '" . (int)$order_id . "' AND os.language_id = o.language_id");
		
		return $query->row;
	}
	
	public function getOrderProductOptions($order_id, $order_product_id)
	{
		$query = $this->get('order_option', '*', array('order_id' => $order_id, 'order_product_id' => $order_product_id));
		
		return $query->rows;
	}
	
	public function confirm($order_id, $order_status_id, $comment = '', $notify = false)
	{
		$order_info = $this->getOrder($order_id);
		
		//order does not exist or has already been processed
		if (!$order_info || $order_info['order_status_id']) {
			return false;
		}
		
		// Fraud Detection
		if ($this->config->get('config_fraud_detection')) {
			$risk_score = $this->Model_Checkout_Fraud->getFraudScore($order_info);
			
			if ($risk_score > $this->config->get('config_fraud_score')) {
				$order_status_id = $this->config->get('config_fraud_status_id');
			}
		}

		// Blacklist
		$status = false;
		
		if ($order_info['customer_id']) {
			$results = $this->Model_Account_Customer->getIps($order_info['customer_id']);
			
			foreach ($results as $result) {
				if ($this->Model_Account_Customer->isBlacklisted($result['ip'])) {
					$status = true;
					
					break;
				}
			}
		} else {
			$status = $this->Model_Account_Customer->isBlacklisted($order_info['ip']);
		}
		
		if ($status) {
			$order_status_id = $this->config->get('config_order_status_id');
		}
		
		$values = array(
			'order_status_id' => $order_status_id,
			'date_modified'	=> $this->tool->format_datetime()
		);
		
		$this->update('order', $values, $order_id);

		$values = array(
			'order_id' => $order_id,
			'order_status_id' => $order_status_id,
			'notify'			=> 1,
			'comment'			=> ($comment && $notify) ? $comment : '',
			'date_added'		=> $this->tool->format_datetime()
		);
		
		$this->insert('order_history', $values);

		$order_product_query = $this->query("SELECT op.*, p.manufacturer_id, p.cost FROM " . DB_PREFIX . "order_product op LEFT JOIN " . DB_PREFIX . "product p ON (op.product_id=p.product_id) WHERE op.order_id = '" . (int)$order_id . "'");
		
		// Products
		$order_info['order_products'] = array();
		
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
			
			$order_info['order_products'][] = $product;
		}
		
		$this->cache->delete('product');
		
		// Downloads
		$order_download_query = $this->query("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");
		
		$order_info['order_downloads'] = $order_download_query->rows;
		
		// Gift Voucher
		$order_voucher_query = $this->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
		
		foreach ($order_voucher_query->rows as $order_voucher) {
			$voucher_id = $this->Model_Cart_Voucher->addVoucher($order_id, $order_voucher);
			
			$this->query("UPDATE " . DB_PREFIX . "order_voucher SET voucher_id = '" . (int)$voucher_id . "' WHERE order_voucher_id = '" . (int)$order_voucher['order_voucher_id'] . "'");
		}
		
		$order_info['order_vouchers'] = $order_voucher_query->rows;
		
		// Send out any gift voucher mails
		if (!empty($order_info['order_vouchers']) && $this->config->get('config_complete_status_id') == $order_status_id) {
			$this->Model_Cart_Voucher->confirm($order_id);
		}
		
				
		// Order Totals
		$order_total_query = $this->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");
		
		foreach ($order_total_query->rows as $order_total) {
			if (method_exists($this->{'model_total_' . $order_total['code']}, 'confirm')) {
				$this->{'model_total_' . $order_total['code']}->confirm($order_info, $order_total);
			}
		}
		
		$order_info['order_totals'] = $order_total_query->rows;
		
		
		//Order Status
		$order_status_query = $this->db->query("SELECT name FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
		
		$order_info['order_status'] = $order_status_query->num_rows ? $order_status_query->row['name'] : '';
		
		//Comments
		$order_info['notify_comment'] = ($comment && $notify) ? nl2br($comment) : '';
		
		//TODO: we can do better than this!
		$this->callController('mail/order', $order_info);
		
		
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
			$this->mail->setSender($order_info['store_name']);
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
			
			$files = $this->generate_excel_order_invoice($v, $order_info);
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
					$this->mail->setCopyTo($this->config->get('config_email'));
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

	public function generate_excel_order_invoice($vendor, $order_info)
	{
		_require(DIR_SYSTEM . 'php-excel/classes/PHPExcel/IOFactory.php');
		
		if (!is_dir(DIR_EXCEL_FPO . $vendor['vendor_id'])) {
			$mode = octdec($this->config->get('config_default_dir_mode'));
			mkdir(DIR_EXCEL_FPO . $vendor['vendor_id'], $mode, true);
			chmod(DIR_EXCEL_FPO . $vendor['vendor_id'], $mode);
		}
		
		$invoice_id = $order_info['invoice_prefix'] . $order_info['invoice_no'] . $order_info['order_id'];
		
		
		$total_lines = count($order_info['order_products']);
		
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
			$sheet->setCellValue(chr($col).$row++,$order_info['shipping_firstname'] . " " . $order_info['shipping_lastname']);
			if(!empty($order_info['shipping_company']))
				$sheet->setCellValue(chr($col).$row++,$order_info['shipping_company']);
			$sheet->setCellValue(chr($col).$row++,$order_info['shipping_address_1']);
			if(!empty($order_info['shipping_address_2']))
				$sheet->setCellValue(chr($col).$row++,$order_info['shipping_address_2']);
			$sheet->setCellValue(chr($col).$row++, $order_info['shipping_city'] . ', ' . $order_info['shipping_zone'] . ' ' . $order_info['shipping_postcode']);
			$sheet->setCellValue(chr($col).$row++, $order_info['shipping_country']);
			
			$sheet->setCellValue('A17',$order_info['shipping_method']);
			
			$row = 20;
			foreach ($order_info['order_products'] as $p) {
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
			
			$this->query("UPDATE " . DB_PREFIX . "order SET fpo = '" . $this->db->escape($fpo) . "' WHERE order_id = '$order_info[order_id]'");
		}
		
		$compare = function ($a,$b)
 {return $a['manufacturer_id'] > $b['manufacturer_id'];};
		
		uasort($order_info['order_products'],$compare);
		
		$tax = null;
		$shipping = null;
		$coupon = array();
		foreach ($order_info['order_totals'] as $t) {
			if($t['code'] == 'shipping')
				$shipping = $t['value'];
			elseif($t['code'] == 'tax')
				$tax = $t;
			elseif($t['code'] == 'coupon')
				$coupon[] = $t;
		}
		
		
		$m_count = array();
		foreach ($order_info['order_products'] as $p) {
			$m_count[$p['manufacturer_id']] = 1;
		}
		
		$total_lines = (count($m_count)*3) + count($order_info['order_products']) + count($coupon);
		
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
			$sheet->setCellValue('E6',$order_info['customer_id']);
			
			$row = 9;
			$col = ord('B');
			$sheet->setCellValue(chr($col).$row++,$order_info['shipping_firstname'] . " " . $order_info['shipping_lastname']);
			if(!empty($order_info['shipping_company']))
				$sheet->setCellValue(chr($col).$row++,$order_info['shipping_company']);
			$sheet->setCellValue(chr($col).$row++,$order_info['shipping_address_1']);
			if(!empty($order_info['shipping_address_2']))
				$sheet->setCellValue(chr($col).$row++,$order_info['shipping_address_2']);
			$sheet->setCellValue(chr($col).$row++, $order_info['shipping_city'] . ', ' . $order_info['shipping_zone'] . ' ' . $order_info['shipping_postcode']);
			$sheet->setCellValue(chr($col).$row++, $order_info['shipping_country']);
			$sheet->setCellValue(chr($col).$row++, $order_info['email']);
			
			$row = 9;
			$col = ord('E');
			$sheet->setCellValue(chr($col).$row++,$order_info['payment_firstname'] . " " . $order_info['payment_lastname']);
			if(!empty($order_info['payment_company']))
				$sheet->setCellValue(chr($col).$row++,$order_info['payment_company']);
			$sheet->setCellValue(chr($col).$row++,$order_info['payment_address_1']);
			if(!empty($order_info['payment_address_2']))
				$sheet->setCellValue(chr($col).$row++,$order_info['payment_address_2']);
			$sheet->setCellValue(chr($col).$row++, $order_info['payment_city'] . ', ' . $order_info['payment_zone'] . ' ' . $order_info['payment_postcode']);
			$sheet->setCellValue(chr($col).$row++, $order_info['payment_country']);
			$sheet->setCellValue(chr($col).$row++, $order_info['email']);
			
			
			$ship_from_style = array('font'=>array('bold'=>true,'size'=>'13'),
											'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
											);
			
			$manufacturers = $this->Model_Catalog_Manufacturer->getManufacturers();
			
			$row = 17;
			$curr_man = '';
			foreach ($order_info['order_products'] as $p) {
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
	
	
	public function update_order($order_id, $order_status_id, $comment = '', $notify = false)
	{
		
		//TODO Remove this once we have fixed issues here...
		trigger_error("Order::update_order() called! This has not been tested / properly implemented yet!");
		
		
		$order_info = $this->getOrder($order_id);

		if ($order_info && $order_info['order_status_id']) {
			// Fraud Detection
			if ($this->config->get('config_fraud_detection')) {
				$risk_score = $this->Model_Checkout_Fraud->getFraudScore($order_info);
				
				if ($risk_score > $this->config->get('config_fraud_score')) {
					$order_status_id = $this->config->get('config_fraud_status_id');
				}
			}

			// Blacklist
			$status = false;
			
			if ($order_info['customer_id']) {
				$results = $this->Model_Account_Customer->getIps($order_info['customer_id']);
				
				foreach ($results as $result) {
					if ($this->Model_Account_Customer->isBlacklisted($result['ip'])) {
						$status = true;
						
						break;
					}
				}
			} else {
				$status = $this->Model_Account_Customer->isBlacklisted($order_info['ip']);
			}
			
			if ($status) {
				$order_status_id = $this->config->get('config_order_status_id');
			}
						
			$this->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
		
			$this->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	
			// Send out any gift voucher mails
			//TODO : Need to impement voucher confirm first!
			if (false || $this->config->get('config_complete_status_id') == $order_status_id) {
				$this->Model_Cart_Voucher->confirm($order_id);
			}
			
			//TODO: Move this to mail/order controller
			if ($notify) {
				$language = $this->language->fetch('mail/order', $order_info['language_directory']);
				$language += $this->language->fetch($order_info['language_filename'], $order_info['language_directory']);
				
				$subject = sprintf($language['text_update_subject'], html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);
	
				$message  = $language['text_update_order'] . ' ' . $order_id . "\n";
				$message .= $language['text_update_date_added'] . ' ' . $this->tool->format_datetime($order_info['date_added'], $language['date_format_short']) . "\n\n";
				
				$order_status_query = $this->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
				
				if ($order_status_query->num_rows) {
					$message .= $language['text_update_order_status'] . "\n\n";
					$message .= $order_status_query->row['name'] . "\n\n";
				}
				
				if ($order_info['customer_id']) {
					$message .= $language['text_update_link'] . "\n";
					$message .= $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id . "\n\n";
				}
				
				if ($comment) {
					$message .= $language['text_update_comment'] . "\n\n";
					$message .= $comment . "\n\n";
				}
					
				$message .= $language['text_update_footer'];

				$this->mail->init();
								
				$this->mail->setTo($order_info['email']);
				$this->mail->setFrom($this->config->get('config_email'));
				$this->mail->setSender($order_info['store_name']);
				$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$this->mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$this->mail->send();
			}
		}
	}
}
