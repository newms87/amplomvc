<?php
class Admin_Controller_Setting_ReturnReason extends Controller
{
	public function index()
	{
		$this->template->load('setting/return_reason');
		$this->language->load('setting/return_reason');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$return_reasons = !empty($_POST['return_reasons']) ? $_POST['return_reasons'] : array();
			
			$this->config->save('product_return', 'return_reasons', $return_reasons, 0, false);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				$this->url->redirect($this->url->link('setting/setting'));
			}
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_store_list'), $this->url->link('setting/store'));
		$this->breadcrumb->add($this->_('text_settings'), $this->url->link('setting/setting'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('setting/return_reason'));
		
		$this->data['save'] = $this->url->link('setting/return_reason');
		$this->data['cancel'] = $this->url->link('setting/store');
		
		if (!$this->request->isPost()) {
			$return_reasons = $this->config->load('product_return', 'return_reasons', 0);
		} else {
			$return_reasons = $_POST['return_reasons'];
		}
		
		if (!$return_reasons) {
			$return_reasons = array();
		}
		
		//If associated to a return, set flag to hide delete button
		foreach ($return_reasons as $return_reason_id => &$return_reason) {
			$filter = array(
				'return_reason_ids' => array($return_reason_id),
			);
			
			$return_total = $this->Model_Sale_Return->getTotalReturns($filter);
			
			if ($return_total) {
				$return_reason['no_delete'] = true;
			}
		} unset($return_reason);
		
		//Add in the template row
		$defaults = array(
			'title' => $this->_('entry_title'),
		);
		
		$this->builder->addTemplateRow($return_reasons, $defaults);
		
		$this->data['template_defaults'] = $defaults;
		
		//Get the Field Translations
		$translate_fields = array(
			'title',
		);
		
		foreach ($return_reasons as $key => &$return_reason) {
			$return_reason['translations'] = $this->translation->getTranslations('return_reasons', $key, $translate_fields);
		} unset($return_reason);

		$this->data['return_reasons'] = $return_reasons;
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'setting/return_reason')) {
			$this->error['permission'] = $this->_('error_permission');
		}
		
		foreach ($_POST['return_reasons'] as $key => $return_reason) {
			if (!$this->validation->text($return_reason['title'], 3, 64)) {
				$this->error["return_reasons[$key][title]"] = $this->_('error_return_reason_title');
			}
		}
		
		$return_reasons = $this->config->load('product_return', 'return_reasons', 0);
		
		if (!empty($return_reasons)) {
			$deleted = array_diff_key($return_reasons, $_POST['return_reasons']);
			
			foreach ($deleted as $return_reason_id => $return_reason) {
				$filter = array(
					'return_reason_ids' => array($return_reason_id),
				);
				
				$return_total = $this->Model_Sale_Return->getTotalReturns($filter);
				
				if ($return_total) {
					$this->error["return_reasons[$return_reason_id][title]"] = $this->_('error_return_reason', $return_reason['title']);
					
					//Add the Return Reason back into the list
					$_POST['return_reasons'][$return_reason_id] = $return_reasons[$return_reason_id];
				}
			}
		}
		
		return $this->error ? false : true;
	}
}
