<?php
class Admin_Controller_Sale_Voucher extends Controller
{
	public function index()
	{
		$this->getList();
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

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified vouchers!"));

				$this->url->redirect('sale/voucher');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		if (isset($_GET['voucher_id']) && $this->validateDelete()) {
			$this->Model_Sale_Voucher->deleteVoucher($_GET['voucher_id']);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified vouchers!"));

				$this->url->redirect('sale/voucher');
			}
		}

		$this->getList();
	}

	public function batch_update()
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

			if (!$this->error && !$this->message->hasError()) {
				$this->message->add('success', _l("Success: You have modified vouchers!"));

				$this->url->redirect('sale/voucher', $this->url->getQueryExclude('action'));
			}
		}

		$this->getList();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Gift Voucher"));

		//The Template
		$this->view->load('sale/voucher_list');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Voucher List"), $this->url->link('sale/voucher'));

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

		$url_query = $this->url->getQueryExclude('voucher_id');

		foreach ($vouchers as &$voucher) {
			$voucher['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => $this->url->link('sale/voucher/update', 'voucher_id=' . $voucher['voucher_id'])
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => $this->url->link('sale/voucher/delete', 'voucher_id=' . $voucher['voucher_id'] . '&' . $url_query)
				)
			);

			$voucher['amount']     = $this->currency->format($voucher['amount'], $this->config->get('config_currency'));
			$voucher['date_added'] = $this->date->format($voucher['date_added'], 'short');
		}
		unset($voucher);

		//Build The Table
		$tt_data = array(
			'row_id' => 'voucher_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($vouchers);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);

		$this->data['list_view'] = $this->table->render();

		//Batch Actions
		$this->data['batch_actions'] = array(
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

		$this->data['batch_update'] = 'sale/voucher/batch_update';

		//Render Limit Menu
		$this->data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $voucher_total;

		$this->data['pagination'] = $this->pagination->render();

		//Action Buttons
		$this->data['insert'] = $this->url->link('sale/voucher/update');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		//Page Head
		$this->document->setTitle(_l("Gift Voucher"));

		//The Template
		$this->view->load('sale/voucher_form');

		//Insert or Update
		$voucher_id = isset($_GET['voucher_id']) ? (int)$_GET['voucher_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Gift Voucher"), $this->url->link('sale/voucher'));

		if ($voucher_id) {
			$this->breadcrumb->add(_l("Edit"), $this->url->link('sale/voucher/update', 'voucher_id=' . $voucher_id));
		} else {
			$this->breadcrumb->add(_l("Add"), $this->url->link('sale/voucher/update'));
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
				$this->data[$key] = $_POST[$key];
			} elseif (isset($voucher_info[$key])) {
				$this->data[$key] = $voucher_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		//Template Data
		$this->data['data_voucher_themes'] = $this->Model_Sale_VoucherTheme->getVoucherThemes();

		$this->data['voucher_id'] = $voucher_id;

		$this->data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Ajax Urls
		$this->data['url_history'] = $this->url->link('sale/voucher/history', 'voucher_id=' . $voucher_id);

		//Action Buttons
		$this->data['send']   = $this->url->link('sale/voucher/send', 'voucher_id=' . $voucher_id);
		$this->data['save']   = $this->url->link('sale/voucher/update', 'voucher_id=' . $voucher_id);
		$this->data['cancel'] = $this->url->link('sale/voucher');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
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

		return $this->error ? false : true;
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
				$this->error['warning'] = _l("Warning: This voucher cannot be deleted as it is part of an <a href=\"%s\">order</a>!", $this->url->link('sale/order/info', 'order_id=' . (int)$order_id));
				break;
			}
		}

		return $this->error ? false : true;
	}

	public function history()
	{
		$this->view->load('sale/voucher_history');
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$this->data['histories'] = array();

		$results = $this->Model_Sale_Voucher->getVoucherHistories($_GET['voucher_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$this->data['histories'][] = array(
				'order_id'   => $result['order_id'],
				'customer'   => $result['customer'],
				'amount'     => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'date_added' => $this->date->format($result['date_added'], 'short'),
			);
		}

		$history_total = $this->Model_Sale_Voucher->getTotalVoucherHistories($_GET['voucher_id']);

		$this->pagination->init();
		$this->pagination->total  = $history_total;
		$this->data['pagination'] = $this->pagination->render();


		$this->response->setOutput($this->render());
	}

	public function send()
	{
		$json = array();

		if (!$this->user->can('modify', 'sale/voucher')) {
			$json['error'] = _l("Warning: You do not have permission to modify vouchers!");
		} else {
			$voucher_id = isset($_GET['voucher_id']) ? $_GET['voucher_id'] : false;

			if ($voucher_id) {
				$voucher = $this->System_Model_Voucher->getVoucher($voucher_id);
			} else {
				$json['error'] = _l("You did not provide the voucher ID");
			}
		}

		if (!$json) {
			$this->mail->sendTemplate('voucher', $voucher);

			$json['success'] = _l("Success: Gift Voucher e-mail has been sent!");
		}

		$this->response->setOutput(json_encode($json));
	}
}
