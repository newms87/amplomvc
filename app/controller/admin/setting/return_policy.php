<?php
/**
 * Title: Return Policy
 * Icon: return_policy_icon.png
 * Order: 7
 */

class App_Controller_Admin_Setting_ReturnPolicy extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Return Policies"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Stores"), site_url('admin/setting/store'));
		$this->breadcrumb->add(_l("Settings"), site_url('admin/setting/setting'));
		$this->breadcrumb->add(_l("Return Policies"), site_url('admin/setting/return_policy'));

		//Load Information
		if ($this->request->isPost() && $this->validate()) {
			$return_policies = !empty($_POST['return_policies']) ? $_POST['return_policies'] : array();

			$this->config->save('policies', 'return_policies', $return_policies, 0, false);

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("You have successfully updated Return Policies"));
				redirect('admin/setting/setting');
			}
		}

		//Load Data or Defaults
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
		}
		unset($return_policy);

		//Add in the template row
		$return_policies['__ac_template__'] = array(
			'title'       => _l("Return Policy Title"),
			'description' => _l("Return Policy Description"),
			'days'        => 14,
		);

		//Get the Field Translations
		$translate_fields = array(
			'title',
			'description',
		);

		foreach ($return_policies as $key => &$return_policy) {
			$return_policy['translations'] = $this->translation->getTranslations('return_policies', $key, $translate_fields);
		}
		unset($return_policy);

		$data['return_policies'] = $return_policies;

		//Template Data
		$data['data_days'] = array(
			'final' => _l("Final Sale"),
			0       => _l("Return Anytime"),
			1       => _l("Days:"),
		);

		//Action Buttons
		$data['save']   = site_url('admin/setting/return_policy');
		$data['cancel'] = site_url('admin/setting/store');

		//Render
		$this->response->setOutput($this->render('setting/return_policy', $data));
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'setting/return_policy')) {
			$this->error['permission'] = _l("You do not have permission to modify Return Policies");
		}

		foreach ($_POST['return_policies'] as $key => $return_policy) {
			if (!$this->validation->text($return_policy['title'], 3, 64)) {
				$this->error["return_policies[$key][title]"] = _l("The Title must be between 3 and 64 characters!");
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
					$this->error["return_policies[$return_policy_id][title]"] = _l("You cannot delete the Return Policy %s because it is associated to a product!", $return_policy['title']);

					//Add the Return Policy back into the list
					$_POST['return_policies'][$return_policy_id] = $return_policies[$return_policy_id];
				}
			}
		}

		return empty($this->error);
	}
}
