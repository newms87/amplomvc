<?php
class Admin_Controller_Total_Coupon extends Controller
{


	public function index()
	{
		$this->template->load('total/coupon');

		$this->language->load('total/coupon');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && ($this->validate())) {
			$this->System_Model_Setting->editSetting('coupon', $_POST);

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
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('total/coupon'));

		$this->data['action'] = $this->url->link('total/coupon');

		$this->data['cancel'] = $this->url->link('extension/total');

		if (isset($_POST['coupon_status'])) {
			$this->data['coupon_status'] = $_POST['coupon_status'];
		} else {
			$this->data['coupon_status'] = $this->config->get('coupon_status');
		}

		if (isset($_POST['coupon_sort_order'])) {
			$this->data['coupon_sort_order'] = $_POST['coupon_sort_order'];
		} else {
			$this->data['coupon_sort_order'] = $this->config->get('coupon_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'total/coupon')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
	}
}
