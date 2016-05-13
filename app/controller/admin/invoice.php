<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

class App_Controller_Admin_Invoice extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("Invoice"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Invoice"), site_url('admin/invoice'));

		//Listing
		$data['listing'] = $this->listing();

		//Batch Actions
		$actions = array();

		if (user_can('w', 'admin/invoice/save')) {
			$actions['cancel'] = array(
				'label' => _l("Cancel")
			);
		}

		if (user_can('w', 'admin/invoice/remove')) {
			$actions['remove'] = array(
				'label' => _l("Remove")
			);
		}

		$data['batch_action'] = array(
			'actions' => $actions,
			'url'     => site_url('admin/invoice/batch-action'),
		);

		//Response
		output($this->render('invoice/list', $data));
	}

	public function listing()
	{
		$required_columns = array(
			'status' => 1,
		);

		$sort   = (array)_get('sort', array('invoice_id' => 'DESC'));
		$filter = (array)_get('filter');

		$columns = $this->Model_Invoice->getColumns((array)_request('columns'));

		if (isset($columns['customer']) && !isset($columns['customer_id'])) {
			$columns['customer_id'] = 1;
		}
		$options = array(
			'index'   => 'invoice_id',
			'page'    => _get('page'),
			'limit'   => _get('limit', option('admin_list_limit', 20)),
			'columns' => $columns + $required_columns
		);

		list($invoices, $invoice_total) = $this->Model_Invoice->getRecords($sort, $filter, $options, true);

		foreach ($invoices as $invoice_id => &$invoice) {
			$actions = array();

			if (user_can('w', 'admin/invoice/paid') && $invoice['status'] != App_Model_Invoice::STATUS_PAID) {
				$actions['paid'] = array(
					'text' => _l("Paid"),
					'href' => site_url('admin/invoice/paid', 'invoice_id=' . $invoice_id),
				);
			}

			if (user_can('r', 'admin/invoice/generate')) {
				$actions['view'] = array(
					'text' => _l("View"),
					'href' => site_url('admin/invoice/generate', 'invoice_id=' . $invoice_id)
				);
			}

			if (user_can('w', 'admin/invoice/cancel') && $invoice['status'] != App_Model_Invoice::STATUS_CANCELLED) {
				$actions['cancel'] = array(
					'text'          => _l("Cancel"),
					'href'          => site_url('admin/invoice/cancel', 'invoice_id=' . $invoice_id),
					'#data-confirm' => '',
				);
			}

			if (user_can('w', 'admin/invoice/remove') && $invoice['status'] != App_Model_Invoice::STATUS_PAID) {
				$actions['delete'] = array(
					'text' => _l("Delete"),
					'href' => site_url('admin/invoice/remove', 'invoice_id=' . $invoice_id)
				);
			}

			$invoice['actions'] = $actions;

			if (isset($columns['customer'])) {
				$customer            = $this->Model_Customer->getRecord($invoice['customer_id']);
				$invoice['customer'] = $customer ? $customer['username'] : _l('No Customer Record');
			}
		}
		unset($invoice);

		$listing = array(
			'extra_cols'     => $this->Model_Invoice->getColumns(false),
			'records'        => $invoices,
			'sort'           => $sort,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total' => $invoice_total,
			'listing_path'   => 'admin/invoice/listing',
			'save_path'      => 'admin/invoice/save',
		);

		$output = block('widget/listing', null, $listing + $options);

		//Response
		if ($this->is_ajax) {
			output($output);
		}

		return $output;
	}

	public function form()
	{
		//Page Head
		set_page_info('title', _l("Invoice Form"));

		//Insert or Update
		$invoice_id  = _get('invoice_id', null);
		$customer_id = _request('customer_id');

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Invoice"), site_url('admin/invoice'));
		breadcrumb($invoice_id ? _l("Update") : _l("New"), site_url('admin/invoice/form', 'invoice_id=' . $invoice_id));

		//The Data
		$invoice = $_POST;

		if ($invoice_id && !IS_POST) {
			$invoice = $this->Model_Invoice->getRecord($invoice_id);
		}

		$defaults = array(
			'invoice_id' => $invoice_id,
		);

		$invoice += $defaults;

		if ($customer_id) {
			$customer = $this->Model_Customer->getRecord($customer_id);
		} else {
			$customer = array();
		}

		$customer += array(
			'customer_id' => '',
			'username'    => '',
		);

		$invoice['customer'] = $customer;

		//Response
		output($this->render('invoice/form', $invoice));
	}

	public function save()
	{
		if ($invoice_id = $this->Model_Invoice->save(_request('invoice_id'), $_POST)) {
			message('success', _l("The Invoice has been updated successfully!"));
			message('data', array('invoice_id' => $invoice_id));
		} else {
			message('error', $this->Model_Invoice->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} elseif ($this->message->has('error')) {
			post_redirect('admin/invoice/form', 'invoice_id=' . $invoice_id);
		} else {
			redirect('admin/invoice');
		}
	}

	public function create()
	{
		if ($invoice_id = $this->Model_Invoice->save(null, $_POST, _request('meta_type'))) {
			message('success', _l("The Invoice has been updated successfully!"));
			message('data', array('invoice_id' => $invoice_id));
		} else {
			message('error', $this->Model_Invoice->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} elseif ($this->message->has('error')) {
			post_redirect('admin/invoice/form');
		} else {
			redirect('admin/invoice/generate', 'invoice_id=' . $invoice_id);
		}
	}

	public function paid()
	{
		if ($this->Model_Invoice->save(_get('invoice_id'), array('status' => App_Model_Invoice::STATUS_PAID))) {
			message('success', _l("The invoice has been paid. The customer's account has been updated."));
		} else {
			message('error', $this->Model_Invoice->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/invoice');
		}
	}

	public function cancel()
	{
		if ($this->Model_Invoice->save(_get('invoice_id'), array('status' => App_Model_Invoice::STATUS_CANCELLED))) {
			message('success', _l("Invoice has been cancelled."));
		} else {
			message('error', $this->Model_Invoice->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/invoice');
		}
	}

	public function remove()
	{
		if ($this->Model_Invoice->remove(_get('invoice_id'))) {
			message('success', _l("Invoice was removed"));
		} else {
			message('error', $this->Model_Invoice->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/invoice');
		}
	}

	public function batch_action()
	{
		$batch  = (array)_request('batch');
		$action = _request('action');
		$value  = _request('value');

		foreach ($batch as $invoice_id) {
			switch ($action) {
				case 'cancel':
					$this->Model_Invoice->save($invoice_id, array('status' => App_Model_Invoice::STATUS_CANCELLED));
					break;

				case 'remove':
					if (user_can('w', 'admin/invoice/remove')) {
						$this->Model_Invoice->remove($invoice_id);
					}
					break;
			}
		}

		if ($this->Model_Invoice->hasError()) {
			message('error', $this->Model_Invoice->fetchError());
		} else {
			message('success', _l("Invoices were updated successfully!"));
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/invoice');
		}
	}

	public function generate()
	{
		//Page Head
		set_page_info('title', _l("Invoice Preview"));

		$invoice_id = _get('invoice_id');

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Invoice"), site_url('admin/invoice'));
		breadcrumb(_l("Preview"), site_url('admin/invoice/generate', 'invoice_id=' . $invoice_id));

		if (!$invoice_id) {
			message('notify', _l("Cannot generate an invoice without the invoice ID"));
			redirect('admin/invoice');
		}

		$this->document->addStyle($this->theme->getThemeStyle(), 'stylesheet', 'print');
		$this->document->addStyle(theme_dir('css/invoice.less'));
		$this->document->addStyle(theme_dir('css/invoice.less'), 'stylesheet', 'print');
		$this->document->addStyle(theme_dir('css/invoice_print.less'), 'stylesheet', 'print');

		$invoice = $this->Model_Invoice->getRecord($invoice_id);

		if (!$invoice) {
			message('error', _l("Unable to locate invoice ID %s", $invoice_id));
			redirect('admin/invoice');
		}

		$customer = $this->Model_Customer->getRecord($invoice['customer_id']);

		if ($customer) {
			$customer += $this->Model_Customer->getMeta($invoice['customer_id']);

			if ($customer) {
				$customer['customer_id'] = $customer['client_id'];
			}
		}

		$customer += array(
			'customer_id' => '',
			'address'     => '',
		);

		$order_ids = !empty($invoice['data']['line_items']) ? $invoice['data']['line_items'] : array();
		$orders    = array();

		if (!empty($order_ids)) {
			$orders = $this->Model_Order->getRecords(array('date_created' => 'ASC'), array('order_id' => $order_ids));
		}

		$total = 0;

		foreach ($orders as &$order) {
			$total += $order['price'];
		}
		unset($order);

		$invoice += array(
			'orders'   => $orders,
			'customer' => $customer,
			'total'    => $total,
		);

		output($this->render('invoice/template', $invoice));
	}
}
