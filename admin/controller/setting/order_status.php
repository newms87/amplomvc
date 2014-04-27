<?php
/**
 * Title: Order Statuses
 * Icon: order_status_icon.png
 * Order: 7
 */

class Admin_Controller_Setting_OrderStatus extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Order Statuses"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Stores"), $this->url->link('setting/store'));
		$this->breadcrumb->add(_l("Settings"), $this->url->link('setting/setting'));
		$this->breadcrumb->add(_l("Order Statuses"), $this->url->link('setting/order_status'));

		//Load Information
		if ($this->request->isPost() && $this->validate()) {
			$order_statuses = !empty($_POST['order_statuses']) ? $_POST['order_statuses'] : array();

			$this->config->save('order', 'order_statuses', $order_statuses, 0, false);

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("You have successfully updated the Order Statuses"));
				$this->url->redirect('setting/setting');
			}
		}

		//Load Data or Defaults
		if (!$this->request->isPost()) {
			$order_statuses = $this->config->load('order', 'order_statuses', 0);
		} else {
			$order_statuses = $_POST['order_statuses'];
		}

		if (!$order_statuses) {
			$order_statuses = array();
		}

		//If associated to an order, set flag to hide delete button
		foreach ($order_statuses as $order_status_id => &$order_status) {
			if ($this->order->orderStatusInUse($order_status_id)) {
				$order_status['no_delete'] = true;
			}
		}
		unset($order_status);


		//Add in the template row
		$defaults = array();

		$order_statuses['__ac_template__'] = array(
			'title' => _l("Status Title"),
		);

		//Get the Field Translations
		$translate_fields = array(
			'title',
		);

		foreach ($order_statuses as $key => &$order_status) {
			$order_status['translations'] = $this->translation->getTranslations('order_statuses', $key, $translate_fields);
		}
		unset($order_status);

		$data['order_statuses'] = $order_statuses;

		//Action Buttons
		$data['save']   = $this->url->link('setting/order_status');
		$data['cancel'] = $this->url->link('setting/store');

		//Render
		$this->response->setOutput($this->render('setting/order_status', $data));
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'setting/order_status')) {
			$this->error['permission'] = _l("You do not have permission to modify Order Statuses");
		}

		foreach ($_POST['order_statuses'] as $key => $order_status) {
			if (!$this->validation->text($order_status['title'], 3, 64)) {
				$this->error["order_statuses[$key][title]"] = _l("The Title must be between 3 and 64 characters!");
			}
		}

		$order_statuses = $this->config->load('order', 'order_statuses', 0);

		//if deleted Order Statuses are associated with an order, do not allow deletion
		if (!empty($order_statuses)) {
			$deleted = array_diff_key($order_statuses, $_POST['order_statuses']);

			foreach ($deleted as $order_status_id => $order_status) {
				if ($this->order->orderStatusInUse($order_status_id)) {
					$this->error["order_statuses[$order_status_id][title]"] = _l("You cannot delete the Order Status %s because it is associated to an order!", $order_status['title']);

					//Add the Order status back into the list
					$_POST['order_statuses'][$order_status_id] = $order_statuses[$order_status_id];
				}
			}
		}

		return empty($this->error);
	}
}
