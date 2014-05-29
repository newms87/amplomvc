<?php
/**
 * Title: Return Reasons
 * Icon: return_reasons_icon.png
 * Order: 7
 */

class App_Controller_Admin_Setting_ReturnReason extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Return Reasons"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Stores"), site_url('admin/setting/store'));
		$this->breadcrumb->add(_l("Settings"), site_url('admin/setting/setting'));
		$this->breadcrumb->add(_l("Return Reasons"), site_url('admin/setting/return_reason'));

		//Load Information
		if ($this->request->isPost() && $this->validate()) {
			$return_reasons = !empty($_POST['return_reasons']) ? $_POST['return_reasons'] : array();

			$this->config->save('product_return', 'return_reasons', $return_reasons, 0, false);

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("You have successfully updated the Return Reasons"));
				redirect('admin/setting/setting');
			}
		}

		//Load Data or Defaults
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
		}
		unset($return_reason);

		//Add in the template row
		$return_reasons['__ac_template__'] = array(
			'title' => _l("Return Reason Title"),
		);

		//Get the Field Translations
		$translate_fields = array(
			'title',
		);

		foreach ($return_reasons as $key => &$return_reason) {
			$return_reason['translations'] = $this->translation->getTranslations('return_reasons', $key, $translate_fields);
		}
		unset($return_reason);

		$data['return_reasons'] = $return_reasons;

		//Action Buttons
		$data['save']   = site_url('admin/setting/return_reason');
		$data['cancel'] = site_url('admin/setting/store');

		//Render
		$this->response->setOutput($this->render('setting/return_reason', $data));
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'setting/return_reason')) {
			$this->error['permission'] = _l("You do not have permission to modify Return Reasons");
		}

		foreach ($_POST['return_reasons'] as $key => $return_reason) {
			if (!$this->validation->text($return_reason['title'], 3, 64)) {
				$this->error["return_reasons[$key][title]"] = _l("The Title must be between 3 and 64 characters!");
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
					$this->error["return_reasons[$return_reason_id][title]"] = _l("You cannot delete the Return Reason %s because it is associated to a return!", $return_reason['title']);

					//Add the Return Reason back into the list
					$_POST['return_reasons'][$return_reason_id] = $return_reasons[$return_reason_id];
				}
			}
		}

		return empty($this->error);
	}
}
