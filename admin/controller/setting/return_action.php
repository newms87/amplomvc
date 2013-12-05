<?php
class Admin_Controller_Setting_ReturnAction extends Controller
{
	public function index()
	{
		//Template and Language
		$this->template->load('setting/return_action');
		$this->language->load('setting/return_action');

		//Page Head
		$this->document->setTitle($this->_('head_title'));

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_store_list'), $this->url->link('setting/store'));
		$this->breadcrumb->add($this->_('text_settings'), $this->url->link('setting/setting'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('setting/return_action'));

		//Load Information
		if ($this->request->isPost() && $this->validate()) {
			$return_actions = !empty($_POST['return_actions']) ? $_POST['return_actions'] : array();

			$this->config->save('product_return', 'return_actions', $return_actions, 0, false);

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				$this->url->redirect('setting/setting');
			}
		}

		//Load Data or Defaults
		if (!$this->request->isPost()) {
			$return_actions = $this->config->load('product_return', 'return_actions', 0);
		} else {
			$return_actions = $_POST['return_actions'];
		}

		if (!$return_actions) {
			$return_actions = array();
		}

		//If associated to a return, set flag to hide delete button
		foreach ($return_actions as $return_action_id => &$return_action) {
			$filter = array(
				'return_action_ids' => array($return_action_id),
			);

			$return_total = $this->Model_Sale_Return->getTotalReturns($filter);

			if ($return_total) {
				$return_action['no_delete'] = true;
			}
		}
		unset($return_action);

		//Add in the template row
		$return_actions['__ac_template__'] = array(
			'title' => $this->_('entry_title'),
		);

		//Get the Field Translations
		$translate_fields = array(
			'title',
		);

		foreach ($return_actions as $key => &$return_action) {
			$return_action['translations'] = $this->translation->getTranslations('return_actions', $key, $translate_fields);
		}
		unset($return_action);

		$this->data['return_actions'] = $return_actions;

		//Action Buttons
		$this->data['save']   = $this->url->link('setting/return_action');
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
		if (!$this->user->can('modify', 'localisation/return_action')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		foreach ($_POST['return_actions'] as $key => $return_action) {
			if (!$this->validation->text($return_action['title'], 3, 64)) {
				$this->error["return_actions[$key][title]"] = $this->_('error_return_action_title');
			}
		}

		$return_actions = $this->config->load('product_return', 'return_actions', 0);

		if (!empty($return_actions)) {
			$deleted = array_diff_key($return_actions, $_POST['return_actions']);

			foreach ($deleted as $return_action_id => $return_action) {
				$filter = array(
					'return_action_ids' => array($return_action_id),
				);

				$return_total = $this->Model_Sale_Return->getTotalReturns($filter);

				if ($return_total) {
					$this->error["return_actions[$return_action_id][title]"] = $this->_('error_return_action', $return_action['title']);

					//Add the Return action back into the list
					$_POST['return_actions'][$return_action_id] = $return_actions[$return_action_id];
				}
			}
		}

		return $this->error ? false : true;
	}
}
