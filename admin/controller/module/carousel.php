<?php
class Admin_Controller_Module_Carousel extends Controller
{


	public function index()
	{
		$this->template->load('module/carousel');

		$this->language->load('module/carousel');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validate()) {
			$this->System_Model_Setting->editSetting('carousel', $_POST);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/module'));
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['image'])) {
			$this->data['error_image'] = $this->error['image'];
		} else {
			$this->data['error_image'] = array();
		}

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('module/carousel'));

		$this->data['action'] = $this->url->link('module/carousel');

		$this->data['cancel'] = $this->url->link('extension/module');

		$this->data['modules'] = array();

		if (isset($_POST['carousel_module'])) {
			$this->data['modules'] = $_POST['carousel_module'];
		} elseif ($this->config->get('carousel_module')) {
			$this->data['modules'] = $this->config->get('carousel_module');
		}

		$this->data['layouts'] = $this->Model_Design_Layout->getLayouts();

		$this->data['banners'] = $this->Model_Design_Banner->getBanners();

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/carousel')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (isset($_POST['carousel_module'])) {
			foreach ($_POST['carousel_module'] as $key => $value) {
				if (!$value['width'] || !$value['height']) {
					$this->error['image'][$key] = $this->_('error_image');
				}
			}
		}

		return $this->error ? false : true;
	}
}
