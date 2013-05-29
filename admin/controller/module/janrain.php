<?php
class ControllerModuleJanrain extends Controller 
{
	
	public function index()
	{
		$this->template->load('module/janrain');

		$this->load->language('module/janrain');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('janrain', $_POST);
					
			$this->message->add('success',$this->_('text_success'));
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('module/janrain'));
				
		$this->data['action'] = $this->url->link('module/janrain');
		$this->data['cancel'] = $this->url->link('extension/module');
		
		$this->data['image_offset'] = array(
				'facebook'=>0,'google'=>1,'linkedin'=>2,'myspace'=>3,'twitter'=>4,'windowslive'=>5,
				'yahoo'=>6,'aol'=>7,'bing'=>8,'flickr'=>9,''=>10,''=>11,''=>12,''=>13,''=>14,
				''=>15,'wordpress'=>16,'paypal'=>17,''=>18,''=>19,''=>20,''=>21
				);
				
		$configs = array(
				'janrain_api_key','janrain_application_domain','janrain_login_redir',
				'janrain_logout_redir','janrain_display_type','janrain_display_icons',
				);
		foreach ($configs as $config) {
			$this->data[$config] = isset($_POST[$config])?$_POST[$config]:$this->config->get($config);
		}
		
		$this->data['image_url'] = $this->image->get('data/rpx-icons16.png');
	
		$this->data['modules'] = array();
		
		if (isset($_POST['janrain_module'])) {
			$this->data['modules'] = $_POST['janrain_module'];
		} elseif ($this->config->get('janrain_module')) {
			$this->data['modules'] = $this->config->get('janrain_module');
		}

		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->children = array(
			'common/header',
			'common/footer',
		);
	
		$this->response->setOutput($this->render());
	}
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/janrain')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
	}
	
	public function install()
	{
		$query = "CREATE TABLE IF NOT EXISTS ".DB_PREFIX."janrain (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`user_id` int(11) NOT NULL,
					`email` varchar(255) NOT NULL,
					`provider` varchar(255) NOT NULL,
					`identifier` varchar(255) NOT NULL,
					`register_date` datetime NOT NULL,
					`lastvisit_date` datetime NOT NULL,
				PRIMARY KEY (`id`)
				)";
		$this->db->query( $query );
	}
	
	public function uninstall()
	{
		$this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "janrain");
	}
}