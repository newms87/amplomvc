<?php
class Admin_Controller_Setting_ShippingPolicy extends Controller
{
	public function index()
	{
		//Template and Language
		$this->template->load('setting/shipping_policy');
		$this->language->load('setting/shipping_policy');

		//Page Head
		$this->document->setTitle($this->_('head_title'));

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_store_list'), $this->url->link('setting/store'));
		$this->breadcrumb->add($this->_('text_settings'), $this->url->link('setting/setting'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('setting/shipping_policy'));

		//Load Information
		if ($this->request->isPost() && $this->validate()) {
			$shipping_policies = !empty($_POST['shipping_policies']) ? $_POST['shipping_policies'] : array();

			$this->config->save('policies', 'shipping_policies', $shipping_policies, 0, false);

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				$this->url->redirect('setting/setting');
			}
		}

		//Load Data or Defaults
		if (!$this->request->isPost()) {
			$shipping_policies = $this->config->load('policies', 'shipping_policies', 0);
		} else {
			$shipping_policies = $_POST['shipping_policies'];
		}

		if (!$shipping_policies) {
			$shipping_policies = array();
		}

		//If associated to a product, set flag to hide delete button
		foreach ($shipping_policies as $shipping_policy_id => &$shipping_policy) {
			$filter = array(
				'shipping_policies' => array($shipping_policy_id),
			);

			if ($this->Model_Catalog_Product->getProducts($filter)) {
				$shipping_policy['no_delete'] = true;
			}
		}
		unset($shipping_policy);

		//Add in the template row
		$shipping_policies['__ac_template__'] = array(
			'title'       => $this->_('entry_title'),
			'description' => $this->_('entry_description'),
		);

		//Get the Field Translations
		$translate_fields = array(
			'title',
			'description',
		);

		foreach ($shipping_policies as $key => &$shipping_policy) {
			$shipping_policy['translations'] = $this->translation->getTranslations('shipping_policies', $key, $translate_fields);
		}
		unset($shipping_policy);

		$this->data['shipping_policies'] = $shipping_policies;

		//Action Buttons
		$this->data['save']   = $this->url->link('setting/shipping_policy');
		$this->data['cancel'] = $this->url->link('setting/store');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'setting/shipping_policy')) {
			$this->error['permission'] = $this->_('error_permission');
		}

		foreach ($_POST['shipping_policies'] as $key => $shipping_policy) {
			if (!$this->validation->text($shipping_policy['title'], 3, 64)) {
				$this->error["shipping_policies[$key][title]"] = $this->_('error_shipping_policy_title');
			}
		}

		$shipping_policies = $this->config->load('policies', 'shipping_policies', 0);

		//if deleted Shipping Policies are associated with a product, do not allow deletion
		if (!empty($shipping_policies)) {
			$deleted = array_diff_key($shipping_policies, $_POST['shipping_policies']);

			foreach ($deleted as $shipping_policy_id => $shipping_policy) {
				$filter = array(
					'shipping_policies' => array($shipping_policy_id),
				);

				if ($this->Model_Catalog_Product->getProducts($filter)) {
					$this->error["shipping_policies[$shipping_policy_id][title]"] = $this->_('error_shipping_policy', $shipping_policy['title']);

					//Add the Shipping policy back into the list
					$_POST['shipping_policies'][$shipping_policy_id] = $shipping_policies[$shipping_policy_id];
				}
			}
		}

		return $this->error ? false : true;
	}
}
