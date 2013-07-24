<?php
class Admin_Controller_Setting_ReturnStatus extends Controller
{
	public function index()
	{
		$this->template->load('setting/return_status');
		$this->language->load('setting/return_status');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$return_statuses = !empty($_POST['return_statuses']) ? $_POST['return_statuses'] : array();
			
			$this->config->save('product_return', 'return_statuses', $return_statuses, 0, false);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				$this->url->redirect($this->url->link('setting/setting'));
			}
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_store_list'), $this->url->link('setting/store'));
		$this->breadcrumb->add($this->_('text_settings'), $this->url->link('setting/setting'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('setting/return_status'));
		
		$this->data['action'] = $this->url->link('setting/return_status');
		$this->data['cancel'] = $this->url->link('setting/store');
		
		if (!$this->request->isPost()) {
			$return_statuses = $this->config->load('product_return', 'return_statuses', 0);
		} else {
			$return_statuses = $_POST['return_statuses'];
		}
		
		if (!$return_statuses) {
			$return_statuses = array();
		}
		
		//If associated to a return, set flag to hide delete button
		foreach ($return_statuses as $return_status_id => &$return_status) {
			$filter = array(
				'return_status_ids' => array($return_status_id),
			);
			
			$return_total = $this->Model_Sale_Return->getTotalReturns($filter);
			
			if (!$return_total) {
				$return_total = $this->Model_Sale_Return->getTotalReturnHistories($filter);
			}
			
			if ($return_total) {
				$return_status['no_delete'] = true;
			}
		} unset($return_status);

		//Defaults
		$defaults = array(
			'title' => $this->_('entry_title'),
		);
		
		if (empty($return_statuses)) {
			$return_statuses[0] = $defaults;
		}
		
		//Add in the template row
		$this->tool->add_template_row($return_statuses);
		
		$this->data['template_row_defaults'] = $defaults;
		
		//Get the Field Translations
		$translate_fields = array(
			'title',
		);
		
		foreach ($return_statuses as $key => &$return_status) {
			$return_status['translations'] = $this->translation->get_translations('return_statuses', $key, $translate_fields);
		} unset($return_status);

		$this->data['return_statuses'] = $return_statuses;
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'setting/return_status')) {
			$this->error['permission'] = $this->_('error_permission');
		}
		
		foreach ($_POST['return_statuses'] as $key => $return_status) {
			if (!$this->validation->text($return_status['title'], 3, 64)) {
				$this->error["return_statuses[$key][title]"] = $this->_('error_return_status_title');
			}
		}

		$return_statuses = $this->config->load('product_return', 'return_statuses', 0);
		
		if (!empty($return_statuses)) {
			$deleted = array_diff_key($return_statuses, $_POST['return_statuses']);
			
			foreach ($deleted as $return_status_id => $return_status) {
				$filter = array(
					'return_status_ids' => array($return_status_id),
				);
				
				$return_total = $this->Model_Sale_Return->getTotalReturns($filter);
				
				if (!$return_total) {
					$return_total = $this->Model_Sale_Return->getTotalReturnHistories($filter);
				}
				
				if ($return_total) {
					$this->error["return_statuses[$return_status_id][title]"] = $this->_('error_return_status', $return_status['title']);
					
					//Add the Return status back into the list
					$_POST['return_statuses'][$return_status_id] = $return_statuses[$return_status_id];
				}
			}
		}
		
		return $this->error ? false : true;
	}
}
