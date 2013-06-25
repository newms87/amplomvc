<?php
class Admin_Controller_Dev_DbAdmin extends Controller
{
	public function index()
	{
		$this->template->load('dev/db_admin');
		$this->load->language('dev/dev');
		
		$this->document->setTitle($this->_('text_db_admin'));
		
		$this->document->addStyle(HTTP_THEME_STYLE . 'dev.css');
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'), '', 0);
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('dev/dev'), '', 1);
		$this->breadcrumb->add($this->_('text_db_admin'), $this->url->link('dev/db_admin'));
		
		$this->data['return'] = $this->url->link('common/home');
		
		//Check for post data
		if ($this->request->isPost() && $this->validate()) {
			if (!empty($_POST['query'])) {
				$results = $this->db->queryRows($_POST['query']);
				
				$this->data['results'] = $results;
			}
		}
		
		$defaults = array(
			'query' => '',
		);

		foreach ($defaults as $key=>$default) {
			if(isset($_POST[$key]))
				$this->data[$key] = $_POST[$key];
			else
				$this->data[$key] = $default;
		}
		
		$this->data['data_tables'] = $this->db->getTables();
		
		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function validate()
	{
		if (!$this->user->hasPermission('modify', 'dev/dev')) {
			$this->error['warning'] = $this->_('error_permission');
		}
				
		return $this->error ? false : true;
	}
}