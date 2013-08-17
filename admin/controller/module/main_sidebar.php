<?php
class Admin_Controller_Module_MainSidebar extends Controller
{


	public function index()
	{
		$this->template->load('module/main_sidebar');

		$this->language->load('module/main_sidebar');

		$this->document->setTitle('Main Sidebar');

		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('main_sidebar', $_POST);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/module'));
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('module/main_sidebar'));

		$this->data['action'] = $this->url->link('module/main_sidebar');

		$this->data['cancel'] = $this->url->link('extension/module');

		$this->data['modules'] = array();

		if (isset($_POST['main_sidebar_module'])) {
			$this->data['modules'] = $_POST['main_sidebar_module'];
		} elseif ($this->config->get('main_sidebar_module')) {
			$this->data['modules'] = $this->config->get('main_sidebar_module');
		}

		$this->data['layouts'] = $this->Model_Design_Layout->getLayouts();

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/main_sidebar')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
	}
}
