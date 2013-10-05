<?php
class Admin_Controller_Extension_ExtTotal_Voucher extends Controller
{
	public function index()
	{
		$this->template->load('total/voucher');

		$this->language->load('total/voucher');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && ($this->validate())) {
			$this->System_Model_Setting->editSetting('voucher', $_POST);

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
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('total/voucher'));

		$this->data['action'] = $this->url->link('total/voucher');

		$this->data['cancel'] = $this->url->link('extension/total');

		if (isset($_POST['voucher_status'])) {
			$this->data['voucher_status'] = $_POST['voucher_status'];
		} else {
			$this->data['voucher_status'] = $this->config->get('voucher_status');
		}

		if (isset($_POST['voucher_sort_order'])) {
			$this->data['voucher_sort_order'] = $_POST['voucher_sort_order'];
		} else {
			$this->data['voucher_sort_order'] = $this->config->get('voucher_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function validate()
	{
		if (!$this->user->hasPermission('modify', 'total/voucher')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		return $this->error;
	}
}
