<?php
class Admin_Controller_Total_SubTotal extends Controller 
{
	
	
	public function index()
	{
		$this->template->load('total/sub_total');

		$this->language->load('total/sub_total');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && ($this->validate())) {
			$this->Model_Setting_Setting->editSetting('sub_total', $_POST);
		
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('total/sub_total'));

		$this->data['action'] = $this->url->link('total/sub_total');
		
		$this->data['cancel'] = $this->url->link('extension/total');

		if (isset($_POST['sub_total_status'])) {
			$this->data['sub_total_status'] = $_POST['sub_total_status'];
		} else {
			$this->data['sub_total_status'] = $this->config->get('sub_total_status');
		}

		if (isset($_POST['sub_total_sort_order'])) {
			$this->data['sub_total_sort_order'] = $_POST['sub_total_sort_order'];
		} else {
			$this->data['sub_total_sort_order'] = $this->config->get('sub_total_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'total/sub_total')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
	}
}