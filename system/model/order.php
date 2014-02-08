<?php
class System_Model_Order extends Model
{
	public function addOrder($data)
	{
		$this->cleanInactiveOrders();

		if (!isset($data['customer_id'])) {
			$data['customer_id'] = 0;
		}

		if (!isset($data['customer_group_id'])) {
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}

		$data['date_added']    = $this->date->now();
		$data['date_modified'] = $this->date->now();

		$data['invoice_id'] = $this->System_Model_Order->generateInvoiceId($data);

		$order_id = $this->insert('order', $data);

		foreach ($data['products'] as $cart_product) {
			$order_product             = $cart_product + $cart_product['product'];
			$order_product['order_id'] = $order_id;

			$order_product_id = $this->insert('order_product', $order_product);

			foreach ($order_product['options'] as $product_option_values) {
				foreach ($product_option_values as $product_option_value) {
					$product_option_value['order_id']         = $order_id;
					$product_option_value['order_product_id'] = $order_product_id;

					$this->insert('order_option', $product_option_value);
				}
			}

			if (!empty($cart_product['downloads'])) {
				foreach ($cart_product['downloads'] as $download) {
					$download['order_id']         = $order_id;
					$download['order_product_id'] = $order_product_id;
					$download['remaining']        = $download['remaining'] * $cart_product['quantity'];

					$this->insert('order_download', $download);
				}
			}
		}

		if (!empty($data['vouchers'])) {
			foreach ($data['vouchers'] as $voucher) {
				$voucher['order_id'] = $order_id;

				$this->insert('order_voucher', $voucher);
			}
		}

		foreach ($data['totals'] as $total) {
			$total['order_id'] = $order_id;

			$this->insert('order_total', $total);
		}

		return $order_id;
	}

	public function editOrder($order_id, $data)
	{
		if (!isset($data['customer_id'])) {
			$data['customer_id']       = 0;
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}

		//Invoice ID cannot be modified
		if (isset($data['invoice_id'])) {
			unset($data['invoice_id']);
		}

		$data['date_modified'] = $this->date->now();

		$this->update('order', $data, $order_id);


		$this->delete('order_product', array('order_id' => $order_id));

		if (!empty($data['products'])) {
			foreach ($data['products'] as $product) {
				$product['order_id'] = $order_id;

				$order_product_id = $this->insert('order_product', $product);

				foreach ($product['option'] as $option) {
					$option['order_id']         = $order_id;
					$option['order_product_id'] = $order_product_id;
					$option['value']            = $option['option_value'];

					$this->insert('order_option', $option);
				}

				if (!empty($product['download'])) {
					foreach ($product['download'] as $download) {
						$download['order_id']         = $order_id;
						$download['order_product_id'] = $order_product_id;
						$download['remaining']        = $download['remaining'] * $product['quantity'];

						$this->insert('order_download', $download);
					}
				}
			}
		}

		$this->delete('order_voucher', array('order_id' => $order_id));

		if (!empty($data['vouchers'])) {
			foreach ($data['vouchers'] as $voucher) {
				$voucher['order_id'] = $order_id;

				$this->insert('order_voucher', $voucher);
			}
		}

		$this->delete('order_total', array('order_id' => $order_id));

		if (!empty($data['totals'])) {
			foreach ($data['totals'] as $total) {
				$total['order_id'] = $order_id;

				$this->insert('order_total', $total);
			}
		}

		//Add Entry to Order History
		$history_data = array(
			'order_id'        => $order_id,
			'order_status_id' => $data['order_status_id'],
			'comment'         => _l("Order Updated by %s", $this->user->info('username')),
			'notify'          => 0,
			'date_added'      => $this->date->now(),
		);

		$this->insert('order_history', $history_data);

		return $order_id;
	}

	public function generateInvoiceId($data)
	{
		$invoice_prefix = $this->config->get('config_invoice_prefix');

		$date_format = null;
		preg_match("/%(.*)%/", $invoice_prefix, $date_format);

		if ($date_format) {
			$invoice_prefix = preg_replace("/%.*%/", $this->date->format(null, $date_format[1]), $invoice_prefix);
		}

		$count = $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "order WHERE invoice_id like '" . $this->db->escape($invoice_prefix) . "%'");

		return $invoice_prefix . $count;
	}

	public function getOrder($order_id)
	{
		return $this->queryRow("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = " . (int)$order_id);
	}

	public function getOrderStatus($order_id)
	{
		return $this->queryVar("SELECT order_status_id FROM " . DB_PREFIX . "order WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getOrders($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "order o";

		//Where
		$where = "1";

		if (!empty($data['order_id'])) {
			$where .= " AND o.order_id = " . (int)$data['order_id'];
		} elseif (!empty($data['order_ids'])) {
			$where .= " AND o.order_id IN (" . implode(',', $data['order_ids']) . ")";
		}

		if (!empty($data['store_ids'])) {
			$where .= " AND o.store_id IN (" . implode(',', $data['store_ids']) . ")";
		}

		if (!empty($data['customer_ids'])) {
			$where .= " AND o.customer_id IN (" . implode(',', $data['customer_ids']) . ")";
		}

		if (isset($data['order_status_id'])) {
			$where .= " AND o.order_status_id = " . (int)$data['order_status_id'];
		} elseif (!empty($data['order_status_ids'])) {
			$where .= " AND o.order_status_id IN (" . implode(',', $data['order_status_ids']) . ")";
		} elseif (!empty($data['!order_status_ids'])) {
			$where .= " AND o.order_status_id NOT IN (" . implode(',', $data['!order_status_ids']) . ")";
		}

		if (isset($data['confirmed'])) {
			$where .= " AND o.confirmed = " . ($data['confirmed'] ? 1 : 0);
		}

		if (!empty($data['total']['low'])) {
			$where .= " AND o.total >= '" . (int)$data['total']['low'] . "'";
		}

		if (!empty($data['total']['high'])) {
			$where .= " AND o.total <= '" . (int)$data['total']['high'] . "'";
		}

		if (!empty($data['date_added']['start'])) {
			$where .= " AND o.date_added >= '" . $this->date->format($data['date_added']['start']) . "'";
		}

		if (!empty($data['date_added']['end'])) {
			$where .= " AND o.date_added <= '" . $this->date->add($data['date_added']['end'], '1 day') . "'";
		}

		if (!empty($data['date_modified']['start'])) {
			$where .= " AND o.date_modified >= '" . $this->date->format($data['date_modified']['start']) . "'";
		}

		if (!empty($data['date_modified']['end'])) {
			$where .= " AND o.date_modified <= '" . $this->date->add($data['date_modified']['end'], '1 day') . "'";
		}

		if (!empty($data['product_ids'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "order_product op ON (op.order_id=o.order_id)";

			$where .= " AND op.product_id IN (" . implode(',', $data['product_ids']) . ")";
		}

		if (!empty($data['language_ids'])) {
			$where .= " AND o.language_id IN (" . implode(',', $data['language_ids']) . ")";
		}

		if (!empty($data['currencies'])) {
			$where .= " AND o.currency_code IN (" . implode(',', $data['currencies']) . ")";
		}

		if (isset($data['years'])) {
			$where .= " AND YEAR(o.date_added) IN ('" . implode("','", $data['years']) . "')";
		}

		if (isset($data['months'])) {
			$where .= " AND MONTH(o.date_added) IN ('" . implode("','", $data['months']) . "')";
		}

		//Order By and Limit
		if (!$total) {
			if (!empty($data['sort'])) {
				$data['order'] = (isset($data['order']) && $data['order'] === 'DESC') ? 'DESC' : 'ASC';

				if ($data['sort'] === 'customer') {
					$from .= " LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id=o.customer_id)";

					$order = "ORDER BY LCASE(CONCAT(c.firstname, c.lastname)) $data[order]";
				}
			}

			if (empty($order)) {
				$order = $this->extractOrder($data);
			}

			$limit = $this->extractLimit($data);
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

	public function getOrderHistories($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "order_history oh";

		//Where
		$where = "1";

		if (!empty($data['order_id'])) {
			$where .= " AND oh.order_id = " . (int)$data['order_id'];
		} elseif (!empty($data['order_ids'])) {
			$where .= " AND oh.order_id IN (" . implode(',', $data['order_ids']) . ")";
		}

		if (!empty($data['order_status_ids'])) {
			$where .= " AND oh.order_status_id IN (" . implode(',', $data['order_status_ids']) . ")";
		}

		//Order By and Limit
		if (!$total) {
			$order = $this->extractOrder($data);
			$limit = $this->extractLimit($data);
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

	public function getConfirmedOrders($data = array(), $select = '', $total = false)
	{
		$data += array(
			'!order_status_ids' => array(0),
		);

		return $this->getOrders($data, $select, $total);
	}

	public function getOrderProducts($order_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = " . (int)$order_id);
	}

	public function getOrderProductOptions($order_id, $order_product_id)
	{
		$options = $this->queryRows("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = " . (int)$order_id . " AND order_product_id = " . (int)$order_product_id);

		foreach ($options as &$option) {
			$option += $this->Model_Catalog_Product->getProductOptionValue($option['product_id'], $option['product_option_id'], $option['product_option_value_id']);
			$option += $this->Model_Catalog_Product->getProductOption($option['product_id'], $option['product_option_id']);
		}
		unset($option);

		return $options;
	}

	public function getOrderTotals($order_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = " . (int)$order_id . " ORDER BY sort_order ASC");
	}

	public function getOrderVouchers($order_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = " . (int)$order_id);
	}

	public function getOrderDownload($order_id, $order_product_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = " . (int)$order_id . " AND order_product_id = " . (int)$order_product_id);
	}

	public function getOrderDownloads($order_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = " . (int)$order_id);
	}

	public function getTotalOrders($data = array())
	{
		return $this->getOrders($data, '', true);
	}

	public function getTotalConfirmedOrders($data = array())
	{
		return $this->getConfirmedOrders($data, '', true);
	}

	public function getTotalOrderHistories($data = array())
	{
		return $this->getOrderHistories($data, '', true);
	}

	public function getTotalOrderProducts($order_id)
	{
		return $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "order_product WHERE order_id = " . (int)$order_id);
	}

	public function getTotalOrderVouchers($order_id)
	{
		return $this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "order_voucher WHERE order_id = " . (int)$order_id);
	}

	public function getGrossSales($data = array())
	{
		$data['!order_status_ids'] = array(0);

		return $this->getOrders($data, '', true);
	}

	public function cleanInactiveOrders()
	{
		$inactive_orders = $this->queryColumn("SELECT order_id FROM " . DB_PREFIX . "order WHERE order_status_id = 0 AND date_modified < DATE_SUB(NOW(), INTERVAL 2 DAY)");

		foreach ($inactive_orders as $order_id) {
			$this->delete('order', $order_id);

			$where = array('order_id' => $order_id);

			$this->delete('order_download', $where);
			$this->delete('order_fraud', $where);
			$this->delete('order_history', $where);
			$this->delete('order_option', $where);
			$this->delete('order_product', $where);
			$this->delete('order_total', $where);
			$this->delete('order_voucher', $where);
		}
	}
}
