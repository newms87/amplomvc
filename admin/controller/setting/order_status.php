<?php
class Admin_Controller_Setting_OrderStatus extends Controller
{
	public function index()
	{
		$this->template->load('setting/order_status');
		$this->language->load('setting/order_status');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$order_statuses = !empty($_POST['order_statuses']) ? $_POST['order_statuses'] : array();
			
			$this->config->save('order', 'order_statuses', $order_statuses, 0, false);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				$this->url->redirect($this->url->link('setting/setting'));
			}
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_store_list'), $this->url->link('setting/store'));
		$this->breadcrumb->add($this->_('text_settings'), $this->url->link('setting/setting'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('setting/order_status'));
		
		$this->data['save'] = $this->url->link('setting/order_status');
		$this->data['cancel'] = $this->url->link('setting/store');
		
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
		} unset($order_status);

		
		//Add in the template row
		$defaults = array(
			'title' => $this->_('entry_title'),
		);
		
		$this->builder->addTemplateRow($order_statuses, $defaults);
		
		$this->data['template_defaults'] = $defaults;
		
		//Get the Field Translations
		$translate_fields = array(
			'title',
		);
		
		foreach ($order_statuses as $key => &$order_status) {
			$order_status['translations'] = $this->translation->getTranslations('order_statuses', $key, $translate_fields);
		} unset($order_status);

		$this->data['order_statuses'] = $order_statuses;
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'setting/order_status')) {
			$this->error['permission'] = $this->_('error_permission');
		}
		
		foreach ($_POST['order_statuses'] as $key => $order_status) {
			if (!$this->validation->text($order_status['title'], 3, 64)) {
				$this->error["order_statuses[$key][title]"] = $this->_('error_order_status_title');
			}
		}

		$order_statuses = $this->config->load('order', 'order_statuses', 0);
		
		//if deleted Order Statuses are associated with an order, do not allow deletion
		if (!empty($order_statuses)) {
			$deleted = array_diff_key($order_statuses, $_POST['order_statuses']);
			
			foreach ($deleted as $order_status_id => $order_status) {
				if ($this->order->orderStatusInUse($order_status_id)) {
					$this->error["order_statuses[$order_status_id][title]"] = $this->_('error_order_status', $order_status['title']);
					
					//Add the Order status back into the list
					$_POST['order_statuses'][$order_status_id] = $order_statuses[$order_status_id];
				}
			}
		}
		
		return $this->error ? false : true;
	}
}
