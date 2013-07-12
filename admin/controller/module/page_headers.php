<?php
class Admin_Controller_Module_PageHeaders extends Controller 
{
	
	
	public function index()
	{
		$this->template->load('module/page_headers');

		$this->language->load('module/page_headers');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Design_Layout->setLayoutPageHeaders($_POST);
			$this->message->add('success', $this->_('text_success'));
			$this->url->redirect($this->url->link('module/page_headers'));
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('module/page_headers'));
		
		$this->data['action'] = $this->url->link('module/page_headers');
		$this->data['cancel'] = $this->url->link('extension/module');

		if (isset($_POST['page_headers'])) {
			$this->data['headers'] = $_POST['page_headers'];
		} else {
			$this->data['headers'] = $this->Model_Design_Layout->getAllPageHeaders();
		}
		
		$layouts = $this->Model_Design_Layout->getLayouts();
		$this->data['layouts'] = array();
		foreach($layouts as $layout)
			$this->data['layouts'][$layout['layout_id']] = $layout['name'];
		
		$this->data['languages'] = $this->Model_Localisation_Language->getLanguages();
		
		//Hide the langugages that are not set
		foreach ($this->data['headers'] as $hid=>&$h) {
			foreach ($h['page_header'] as $lang_id=>$ph) {
				$found = false;
				foreach($this->data['languages'] as $l)
					if($l['language_id'] == $lang_id)
						$found=true;
			}
			if(!$found)
				unset($this->data['headers'][$hid]['page_header'][$lang_id]);
		}
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/page_headers')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach($_POST['page_headers'] as $hid=>$h)
			if(!isset($h['layouts']))
				$this->error['layout_'.$hid] = $this->_('error_no_layouts');
			
		return $this->error ? false : true;
	}
}