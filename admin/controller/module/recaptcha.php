<?php
class Admin_Controller_Module_Recaptcha extends Controller
{


	public function index()
	{
		$this->template->load('module/recaptcha');

		$this->language->load('module/recaptcha');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('recaptcha', $_POST);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/module'));
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['private_key'])) {
			$this->data['error_private_key'] = $this->error['private_key'];
		} else {
			$this->data['error_private_key'] = '';
		}

		if (isset($this->error['public_key'])) {
			$this->data['error_public_key'] = $this->error['public_key'];
		} else {
			$this->data['error_public_key'] = '';
		}

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('module/recaptcha'));

		$this->data['action'] = $this->url->link('module/recaptcha');

		$this->data['cancel'] = $this->url->link('extension/module');

		if (isset($_POST['private_key'])) {
			$this->data['private_key'] = $_POST['recaptcha_private_key'];
		} else {
			$this->data['private_key'] = $this->config->get('recaptcha_private_key');
		}

		if (isset($_POST['public_key'])) {
			$this->data['public_key'] = $_POST['recaptcha_public_key'];
		} else {
			$this->data['public_key'] = $this->config->get('recaptcha_public_key');
		}

		$this->children = array(
			'common/header',
			'common/footer',
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/recaptcha')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$_POST['recaptcha_private_key']) {
			$this->error['private_key'] = $this->_('error_private_key');
		}

		if (!$_POST['recaptcha_public_key']) {
			$this->error['public_key'] = $this->_('error_public_key');
		}

		return $this->error ? false : true;
	}
}
