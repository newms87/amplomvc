<?php
class Admin_Controller_Setting_ReturnPolicy extends Controller
{
	public function index()
	{
		$this->template->load('setting/return_policy');
		$this->language->load('setting/return_policy');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$return_policies = !empty($_POST['return_policies']) ? $_POST['return_policies'] : array();
			
			$this->config->save('policies', 'return_policies', $return_policies, 0, false);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				$this->url->redirect($this->url->link('setting/setting'));
			}
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_store_list'), $this->url->link('setting/store'));
		$this->breadcrumb->add($this->_('text_settings'), $this->url->link('setting/setting'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('setting/return_policy'));
		
		$this->data['save'] = $this->url->link('setting/return_policy');
		$this->data['cancel'] = $this->url->link('setting/store');
		
		if (!$this->request->isPost()) {
			$return_policies = $this->config->load('policies', 'return_policies', 0);
		} else {
			$return_policies = $_POST['return_policies'];
		}
		
		if (!$return_policies) {
			$return_policies = array();
		}
		
		//If associated to a product, set flag to hide delete button
		foreach ($return_policies as $return_policy_id => &$return_policy) {
			$filter = array(
				'return_policies' => array($return_policy_id),
			);
			
			if ($this->Model_Catalog_Product->getProducts($filter)) {
				$return_policy['no_delete'] = true;
			}
		} unset($return_policy);

		//Defaults
		$defaults = array(
			'title' => $this->_('entry_title'),
			'description' => $this->_('entry_description'),
			'days' => 14,
		);
		
		if (empty($return_policies)) {
			$return_policies[0] = $defaults;
		}
		
		//Add in the template row
		$this->tool->add_template_row($return_policies);
		
		$this->data['template_row_defaults'] = $defaults;
		
		$this->data['default_days'] = 14;
		
		//Get the Field Translations
		$translate_fields = array(
			'title',
			'description',
		);
		
		foreach ($return_policies as $key => &$return_policy) {
			$return_policy['translations'] = $this->translation->get_translations('return_policies', $key, $translate_fields);
		} unset($return_policy);

		$this->data['return_policies'] = $return_policies;
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'setting/return_policy')) {
			$this->error['permission'] = $this->_('error_permission');
		}
		
		foreach ($_POST['return_policies'] as $key => $return_policy) {
			if (!$this->validation->text($return_policy['title'], 3, 64)) {
				$this->error["return_policies[$key][title]"] = $this->_('error_return_policy_title');
			}
		}

		$return_policies = $this->config->load('policies', 'return_policies', 0);
		
		//if deleted Return Policies are associated with a product, do not allow deletion
		if (!empty($return_policies)) {
			$deleted = array_diff_key($return_policies, $_POST['return_policies']);
			
			foreach ($deleted as $return_policy_id => $return_policy) {
				$filter = array(
					'return_policies' => array($return_policy_id),
				);
				
				if ($this->Model_Catalog_Product->getProducts($filter)) {
					$this->error["return_policies[$return_policy_id][title]"] = $this->_('error_return_policy', $return_policy['title']);
					
					//Add the Return Policy back into the list
					$_POST['return_policies'][$return_policy_id] = $return_policies[$return_policy_id];
				}
			}
		}
		
		return $this->error ? false : true;
	}
}
