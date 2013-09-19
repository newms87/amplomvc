<?php
class Admin_Controller_Total_Total extends Controller
{


	public function index()
	{
		$this->template->load('total/total');

		$this->language->load('total/total');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && ($this->validate())) {
			$this->System_Model_Setting->editSetting('total', $_POST);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/total'));
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_total'), $this->url->link('extension/total'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('total/total'));

		$this->data['action'] = $this->url->link('total/total');

		$this->data['cancel'] = $this->url->link('extension/total');

		if (isset($_POST['total_status'])) {
			$this->data['total_status'] = $_POST['total_status'];
		} else {
			$this->data['total_status'] = $this->config->get('total_status');
		}

		if (isset($_POST['total_sort_order'])) {
			$this->data['total_sort_order'] = $_POST['total_sort_order'];
		} else {
			$this->data['total_sort_order'] = $this->config->get('total_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'total/total')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
	}
}
