<?php
class App_Controller_Admin_Sale_Voucher extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Gift Vouchers"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Voucher List"), site_url('admin/sale/voucher'));

		//Batch Actions
		$data['batch_actions'] = array(
			'enable'  => array(
				'label' => _l("Enable"),
			),
			'disable' => array(
				'label' => _l("Disable"),
			),
			'copy'    => array(
				'label' => _l("Copy"),
			),
			'delete'  => array(
				'label' => _l("Delete"),
			),
		);

		$data['batch_update'] = 'sale/voucher/batch_action';

		//Action Buttons
		$data['insert'] = site_url('admin/sale/voucher/form');

		//Render
		$this->response->setOutput($this->render('sale/voucher_list', $data));
	}

	public function update()
	{
		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (empty($_GET['voucher_id'])) {
				$this->Model_Sale_Voucher->addVoucher($_POST);
			} //Update
			else {
				$this->Model_Sale_Voucher->editVoucher($_GET['voucher_id'], $_POST);
			}

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified vouchers!"));

				redirect('admin/sale/voucher');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		if (isset($_GET['voucher_id']) && $this->validateDelete()) {
			$this->Model_Sale_Voucher->deleteVoucher($_GET['voucher_id']);

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified vouchers!"));

				redirect('admin/sale/voucher');
			}
		}

		$this->getList();
	}

	private function listing()
	{
		//The Table Columns
		$columns = array();

		$columns['code'] = array(
			'type'         => 'text',
			'display_name' => _l("Code"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['to_name'] = array(
			'type'         => 'text',
			'display_name' => _l("To"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['from_name'] = array(
			'type'         => 'text',
			'display_name' => _l("From"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['theme'] = array(
			'type'         => 'text',
			'display_name' => _l("Theme"),
			'filter'       => false,
			'sortable'     => true,
		);

		$columns['amount'] = array(
			'type'         => 'int',
			'display_name' => _l("Amount"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['date_added'] = array(
			'type'         => 'date',
			'display_name' => _l("Date Added"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['status'] = array(
			'type'         => 'select',
			'display_name' => _l("Status"),
			'filter'       => true,
			'build_data'   => array(
				0 => _l("Disabled"),
				1 => _l("Enabled"),
			),
			'sortable'     => true,
		);

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('code', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$voucher_total = $this->Model_Sale_Voucher->getTotalVouchers($filter);
		$vouchers      = $this->Model_Sale_Voucher->getVouchers($sort + $filter);

		foreach ($vouchers as &$voucher) {
			$voucher['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/sale/voucher/form', 'voucher_id=' . $voucher['voucher_id'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/sale/voucher/delete', 'voucher_id=' . $voucher['voucher_id'])
				)
			);

			$voucher['amount']     = $this->currency->format($voucher['amount'], option('config_currency'));
			$voucher['date_added'] = $this->date->format($voucher['date_added'], 'short');
		}
		unset($voucher);

		$listing = array(
			'row_id'         => 'category_id',
			'columns'        => $columns,
			'rows'           => $vouchers,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $voucher_total,
			'listing_path'   => 'sale/voucher/listing',
		);

		$output = block('widget/listing', null, $listing);

		if ($this->request->isAjax()) {
			$this->response->setOutput($output);
		} else {
			return $output;
		}
	}

	private function getForm()
	{
		//Page Head
		$this->document->setTitle(_l("Gift Voucher"));

		//Insert or Update
		$voucher_id = isset($_GET['voucher_id']) ? (int)$_GET['voucher_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Gift Voucher"), site_url('admin/sale/voucher'));

		if ($voucher_id) {
			$this->breadcrumb->add(_l("Edit"), site_url('admin/sale/voucher/update', 'voucher_id=' . $voucher_id));
		} else {
			$this->breadcrumb->add(_l("Add"), site_url('admin/sale/voucher/update'));
		}

		//Load Information
		if ($voucher_id && !$this->request->isPost()) {
			$voucher_info = $this->Model_Sale_Voucher->getVoucher($voucher_id);
		}

		//Set Values or Defaults
		$defaults = array(
			'code'             => '',
			'from_name'        => '',
			'from_email'       => '',
			'to_name'          => '',
			'to_email'         => '',
			'voucher_theme_id' => '',
			'message'          => '',
			'amount'           => '',
			'status'           => 1,
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$data[$key] = $_POST[$key];
			} elseif (isset($voucher_info[$key])) {
				$data[$key] = $voucher_info[$key];
			} else {
				$data[$key] = $default;
			}
		}

		//Template Data
		$data['data_voucher_themes'] = $this->Model_Sale_VoucherTheme->getVoucherThemes();

		$data['voucher_id'] = $voucher_id;

		$data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Ajax Urls
		$data['url_history'] = site_url('admin/sale/voucher/history', 'voucher_id=' . $voucher_id);

		//Action Buttons
		$data['send']   = site_url('admin/sale/voucher/send', 'voucher_id=' . $voucher_id);
		$data['save']   = site_url('admin/sale/voucher/update', 'voucher_id=' . $voucher_id);
		$data['cancel'] = site_url('admin/sale/voucher');

		//Render
		$this->response->setOutput($this->render('sale/voucher_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'sale/voucher')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify vouchers!");
		}

		if (!$this->validation->text($_POST['code'], 3, 32)) {
			$this->error['code'] = _l("Code must be between 3 and 32 characters!");
		}

		$voucher_id = isset($_GET['voucher_id']) ? (int)$_GET['voucher_id'] : 0;

		$voucher_exists = $this->db->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "voucher WHERE voucher_id != $voucher_id AND code = '" . $this->db->escape($_POST['code']) . "'");

		if ($voucher_exists) {
			$this->error['code'] = _l("Warning: Voucher code is already in use!");
		}

		if (!$this->validation->text($_POST['to_name'], 1, 64)) {
			$this->error['to_name'] = _l("Recipient's Name must be between 1 and 64 characters!");
		}

		if (!$this->validation->email($_POST['to_email'])) {
			$this->error['to_email'] = _l("E-Mail Address does not appear to be valid!");
		}

		if (!$this->validation->text($_POST['from_name'], 1, 64)) {
			$this->error['from_name'] = _l("Your Name must be between 1 and 64 characters!");
		}

		if (!$this->validation->email($_POST['from_email'])) {
			$this->error['from_email'] = _l("E-Mail Address does not appear to be valid!");
		}

		if ((int)$_POST['amount'] < 1) {
			$this->error['amount'] = _l("Amount must be greater than or equal to 1!");
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'sale/voucher')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify vouchers!");
		}

		if (!empty($_GET['selected'])) {
			$voucher_ids = $_GET['selected'];
		} else {
			$voucher_ids = array();
		}

		if (!empty($_GET['voucher_id'])) {
			$voucher_ids[] = $_GET['voucher_id'];
		}

		foreach ($voucher_ids as $voucher_id) {
			$order_id = $this->db->queryVar("SELECT order_id FROM " . DB_PREFIX . "order_voucher WHERE voucher_id = " . (int)$voucher_id);

			if ($order_id) {
				$this->error['warning'] = _l("Warning: This voucher cannot be deleted as it is part of an <a href=\"%s\">order</a>!", site_url('admin/sale/order/info', 'order_id=' . (int)$order_id));
				break;
			}
		}

		return empty($this->error);
	}

	public function batch_action()
	{
		if (!empty($_GET['selected']) && isset($_GET['action'])) {
			if ($_GET['action'] !== 'delete' || $this->validateDelete()) {
				foreach ($_GET['selected'] as $voucher_id) {
					switch ($_GET['action']) {
						case 'enable':
							$this->Model_Sale_Voucher->editVoucher($voucher_id, array('status' => 1));
							break;
						case 'disable':
							$this->Model_Sale_Voucher->editVoucher($voucher_id, array('status' => 0));
							break;
						case 'delete':
							$this->Model_Sale_Voucher->deleteVoucher($voucher_id);
							break;
						case 'copy':
							$this->Model_Sale_Voucher->copyVoucher($voucher_id);
							break;
					}

					if ($this->error) {
						break;
					}
				}
			}

			if (!$this->error && !$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified vouchers!"));

				redirect('admin/sale/voucher', $this->url->getQueryExclude('action'));
			}
		}

		$this->getList();
	}

	public function history()
	{
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$results = $this->Model_Sale_Voucher->getVoucherHistories($_GET['voucher_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['histories'][] = array(
				'order_id'   => $result['order_id'],
				'customer'   => $result['customer'],
				'amount'     => $this->currency->format($result['amount'], option('config_currency')),
				'date_added' => $this->date->format($result['date_added'], 'short'),
			);
		}

		$history_total = $this->Model_Sale_Voucher->getTotalVoucherHistories($_GET['voucher_id']);

		$this->pagination->init();
		$this->pagination->total  = $history_total;
		$data['pagination'] = $this->pagination->render();


		$this->response->setOutput($this->render('sale/voucher_history', $data));
	}

	public function send()
	{
		$json = array();

		if (!$this->user->can('modify', 'sale/voucher')) {
			$json['error'] = _l("Warning: You do not have permission to modify vouchers!");
		} else {
			$voucher_id = isset($_GET['voucher_id']) ? $_GET['voucher_id'] : false;

			if ($voucher_id) {
				$voucher = $this->Model_Sale_Voucher->getVoucher($voucher_id);
			} else {
				$json['error'] = _l("You did not provide the voucher ID");
			}
		}

		if (!$json) {
			call('mail/voucher', $voucher);

			$json['success'] = _l("Success: Gift Voucher e-mail has been sent!");
		}

		$this->response->setOutput(json_encode($json));
	}
}
