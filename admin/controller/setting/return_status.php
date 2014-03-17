<?php
/**
 * Title: Return Statuses
 * Icon: return_status_icon.png
 * Order: 7
 */

class Admin_Controller_Setting_ReturnStatus extends Controller
{
	public function index()
	{
		//Template and Language
		$this->view->load('setting/return_status');
		//Page Head
		$this->document->setTitle(_l("Return Statuses"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Stores"), $this->url->link('setting/store'));
		$this->breadcrumb->add(_l("Settings"), $this->url->link('setting/setting'));
		$this->breadcrumb->add(_l("Return Statuses"), $this->url->link('setting/return_status'));

		//Load Information
		if ($this->request->isPost() && $this->validate()) {
			$return_statuses = !empty($_POST['return_statuses']) ? $_POST['return_statuses'] : array();

			$this->config->save('product_return', 'return_statuses', $return_statuses, 0, false);

			if (!$this->message->hasError()) {
				$this->message->add('success', _l("You have successfully updated the Return Statuses"));
				$this->url->redirect('setting/setting');
			}
		}

		//Load Data or Defaults
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
		}
		unset($return_status);

		//Add in the template row
		$return_statuses['__ac_template__'] = array(
			'title' => _l("Status Title"),
		);

		//Get the Field Translations
		$translate_fields = array(
			'title',
		);

		foreach ($return_statuses as $key => &$return_status) {
			$return_status['translations'] = $this->translation->getTranslations('return_statuses', $key, $translate_fields);
		}
		unset($return_status);

		$this->data['return_statuses'] = $return_statuses;

		//Action Buttons
		$this->data['save']   = $this->url->link('setting/return_status');
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
		if (!$this->user->can('modify', 'setting/return_status')) {
			$this->error['permission'] = _l("You do not have permission to modify Return Statuses");
		}

		foreach ($_POST['return_statuses'] as $key => $return_status) {
			if (!$this->validation->text($return_status['title'], 3, 64)) {
				$this->error["return_statuses[$key][title]"] = _l("The Title must be between 3 and 64 characters!");
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
					$this->error["return_statuses[$return_status_id][title]"] = _l("You cannot delete the Return Status %s because it is associated to a return!", $return_status['title']);

					//Add the Return status back into the list
					$_POST['return_statuses'][$return_status_id] = $return_statuses[$return_status_id];
				}
			}
		}

		return $this->error ? false : true;
	}
}
