<?php
class Admin_Model_Sale_Voucher extends Model 
{
	public function addVoucher($data)
	{
		$data['date_added'] = $this->date->now();
		
		$this->insert('voucher', $data);
	}
	
	public function editVoucher($voucher_id, $data)
	{
		$this->update('voucher', $data, $voucher_id);
	}
	
	public function deleteVoucher($voucher_id)
	{
		$this->delete('voucher', $voucher_id);
		$this->delete('voucher_history', array('voucher_id' => $voucher_id));
	}
	
	public function getVoucher($voucher_id)
	{
		return $this->queryRow("SELECT DISTINCT * FROM " . DB_PREFIX . "voucher WHERE voucher_id = '" . (int)$voucher_id . "'");
	}

	public function getVoucherByCode($code)
	{
		return $this->queryRow("SELECT DISTINCT * FROM " . DB_PREFIX . "voucher WHERE code = '" . $this->db->escape($code) . "'");
	}
		
	public function getVouchers($data = array(), $select = '*', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif(empty($select)) {
			$select = '*';
		}
		
		$select .= ",(SELECT vtd.name FROM " . DB_PREFIX . "voucher_theme_description vtd WHERE vtd.voucher_theme_id = v.voucher_theme_id AND vtd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS theme";
				
		//From
		$from = DB_PREFIX . "voucher v";
		
		//Where
		$where = '1';
		
		//Order and Limit
		if (!$total) {
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$order = '';
			$limit = '';
		}
		
		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";
		
		$result = $this->query($query);
		
		if ($total) {
			return $result->row['total'];
		}
		
		return $result->rows;
	}
		
	public function sendVoucher($voucher_id)
	{
		$voucher_info = $this->getVoucher($voucher_id);
		
		if ($voucher_info) {
			if ($voucher_info['order_id']) {
				$order_id = $voucher_info['order_id'];
			} else {
				$order_id = 0;
			}
			
			$order_info = $this->Model_Sale_Order->getOrder($order_id);
			$voucher_theme_info = $this->Model_Sale_VoucherTheme->getVoucherTheme($voucher_info['voucher_theme_id']);
			
			// If voucher belongs to an order
			if ($order_info) {
				$language = new Language($this->registry, $order_info['language_directory']);
				$language->load($order_info['language_filename']);
				$language->load('mail/voucher');
				
				$data = array(
					'title' => $language->format('text_subject', $voucher_info['from_name']),
					'text_greeting' => $language->format('text_greeting', $this->currency->format($voucher_info['amount'], $order_info['currency_code'], $order_info['currency_value'])),
					'text_from' => $language->format('text_from', $voucher_info['from_name']),
					'text_message' => $language->get('text_message'),
					'text_redeem' => $language->format('text_redeem', $voucher_info['code']),
					'text_footer' => $language->get('text_footer'),
					'message' => nl2br($voucher_info['message']),
					'store_name' => $order_info['store_name'],
					'store_url' => $order_info['store_url'],
					'image' => $this->image->get($voucher_theme_info['image']),
				);
				
				$subject = html_entity_decode($language->format('text_subject', $voucher_info['from_name']), ENT_QUOTES, 'UTF-8');
			
			// If voucher does not belong to an order
			} else {
				$this->language->load('mail/voucher');
				
				$data = array(
					'title' => $this->_('text_subject', $voucher_info['from_name']),
					'text_greeting' => $this->_('text_greeting', $this->currency->format($voucher_info['amount'], $order_info['currency_code'], $order_info['currency_value'])),
					'text_from' => $this->_('text_from', $voucher_info['from_name']),
					'text_message' => $this->language->get('text_message'),
					'text_redeem' => $this->_('text_redeem', $voucher_info['code']),
					'text_footer' => $this->language->get('text_footer'),
					'message' => nl2br($voucher_info['message']),
					'store_name' => $order_info['store_name'],
					'store_url' => $order_info['store_url'],
					'image' => $this->image->get($voucher_theme_info['image']),
				);
				
				$subject = html_entity_decode(sprintf($this->_('text_subject'), $voucher_info['from_name']), ENT_QUOTES, 'UTF-8');
			}
			
			$template = new Template($this->registry);
			
			$template->set_data($data);
			$template->load('mail/voucher');
			
			$this->mail->init();
			
			$this->mail->setTo($voucher_info['to_email']);
			$this->mail->setFrom($this->config->get('config_email'));
			$this->mail->setSender($order_info['store_name']);
			$this->mail->setSubject($subject);
			$this->mail->setHtml($template->render());
			$this->mail->send();
		}
	}
			
	public function getTotalVouchers()
	{
		return $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "voucher");
	}
	
	public function getTotalVouchersByVoucherThemeId($voucher_theme_id)
	{
		return $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "voucher WHERE voucher_theme_id = '" . (int)$voucher_theme_id . "'");
	}
	
	public function getVoucherHistories($voucher_id, $start = 0, $limit = 10)
	{
		return $this->queryRows("SELECT vh.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, vh.amount, vh.date_added FROM " . DB_PREFIX . "voucher_history vh LEFT JOIN `" . DB_PREFIX . "order` o ON (vh.order_id = o.order_id) WHERE vh.voucher_id = '" . (int)$voucher_id . "' ORDER BY vh.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);
	}
	
	public function getTotalVoucherHistories($voucher_id)
	{
		return $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "voucher_history WHERE voucher_id = '" . (int)$voucher_id . "'");
	}
}