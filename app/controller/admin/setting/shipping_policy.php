<?php
/**
 * Title: Shipping Policies
 * Icon: shipping_policy_icon.png
 * Order: 7
 */

class App_Controller_Admin_Setting_ShippingPolicy extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Shipping Policies"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Stores"), site_url('admin/setting/store'));
		$this->breadcrumb->add(_l("Settings"), site_url('admin/setting/setting'));
		$this->breadcrumb->add(_l("Shipping Policies"), site_url('admin/setting/shipping_policy'));

		//Load Information
		if ($this->request->isPost() && $this->validate()) {
			$shipping_policies = !empty($_POST['shipping_policies']) ? $_POST['shipping_policies'] : array();

			$this->config->save('policies', 'shipping_policies', $shipping_policies, 0, false);

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("You have successfully updated Shipping Policies"));
				redirect('admin/setting/setting');
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
			'title'       => _l("Shipping Policy Title"),
			'description' => _l("Shipping Policy Description"),
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

		$data['shipping_policies'] = $shipping_policies;

		//Template Data
		$data['data_days'] = array(
			'final' => _l("Final Sale"),
			0       => _l("Shipping Anytime"),
			1       => _l("Days:"),
		);

		//Action Buttons
		$data['save']   = site_url('admin/setting/shipping_policy');
		$data['cancel'] = site_url('admin/setting/store');

		//Render
		$this->response->setOutput($this->render('setting/shipping_policy', $data));
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'setting/shipping_policy')) {
			$this->error['permission'] = _l("You do not have permission to modify Shipping Policies");
		}

		foreach ($_POST['shipping_policies'] as $key => $shipping_policy) {
			if (!$this->validation->text($shipping_policy['title'], 3, 64)) {
				$this->error["shipping_policies[$key][title]"] = _l("The Title must be between 3 and 64 characters!");
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
					$this->error["shipping_policies[$shipping_policy_id][title]"] = _l("You cannot delete the Shipping Policy %s because it is associated to a product!", $shipping_policy['title']);

					//Add the Shipping policy back into the list
					$_POST['shipping_policies'][$shipping_policy_id] = $shipping_policies[$shipping_policy_id];
				}
			}
		}

		return empty($this->error);
	}
}
