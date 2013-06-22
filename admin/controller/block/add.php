<?php
class Admin_Controller_Block_Add extends Controller 
{
	public function index()
	{
		$this->load->language('block/add');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Block_Block->addBlock($_POST);
			
			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success', $_POST['name']));
				
				$this->url->redirect($this->url->link('block/block', 'name=' . $_POST['route']));
			}
			
			$this->message->add('warning', $this->_('error_add_block'));
		}
		
		$this->message->add('notify', $this->_('notify_add_block'));
		
		$this->getForm();
	}
	
	private function getForm()
	{
		$this->template->load('block/add');
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_block_list'), $this->url->link('block/block'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('block/add'));

		$this->data['action'] = $this->url->link('block/add');
		$this->data['cancel'] = $this->url->link('block/block');
		
		$defaults = array(
			'name' => '',
			'route' => '',
			'language_file' => true,
			'settings_file' => true,
			'profiles_file' => true,
			'themes' => array('default'),
			'front_language_file' => true,
		);
		
		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($block_info[$key])) {
				$this->data[$key] = $block_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		$this->data['data_themes'] = $this->theme->getThemes();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'block/add')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$this->validation->text($_POST['name'], 3, 128)) {
			$this->error['name'] = $this->_('error_name');
		}
		
		if (empty($_POST['route']) || !preg_match("/^[a-z0-9_]+\/[a-z0-9_]+\$/i", $_POST['route'])) {
			$this->error['route'] = $this->_('error_route');
		}
		
		return $this->error ? false : true;
	}
}
