<?php
class ControllerDevDev extends Controller {
	public function index(){
		$this->template->load('dev/dev');
		
		$this->language->load('dev/dev');
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->data['url_sync'] = $this->url->link("dev/dev/sync");
		$this->data['url_site_management'] = $this->url->link("dev/dev/site_management");
		$this->data['url_backup_restore'] = $this->url->link("dev/dev/backup_restore");
		
		$this->content();
	}
	
	public function sync(){
		$this->template->load('dev/sync');

		$this->load->language('dev/dev');
		
		$this->document->setTitle($this->_('text_sync'));
		
		$dev_sites = $this->model_setting_setting->getSetting('dev_sites');
		
		if($_SERVER["REQUEST_METHOD"] == 'POST' && $this->validate()){
			if(isset($_POST['sync_site'])){
				if(!isset($_POST['tables'])){
					$this->message->add('warning', "You must select at least 1 table to sync.");
				}
				else{
					$key = array_search($_POST['domain'], $dev_sites);
					foreach($dev_sites as $site){
						if($_POST['domain'] == $site['domain']){
							$dev_sites[$key]['password'] = $_POST['password'];
							
							$this->dev->request_table_sync($dev_sites[$key], $_POST['tables']);
							
							break;
						}
					}
				}
			}
		}
		
		$this->breadcrumb->add($this->_('text_sync'), $this->url->link('dev/dev/sync'));
		
		$this->data['request_sync_table'] = $this->url->link('dev/dev/request_sync_table');
		
		$defaults = array(
			'tables' => '',
			'domain' => '',
		);

		foreach($defaults as $key=>$default){
			if(isset($_POST[$key]))
				$this->data[$key] = $_POST[$key];
			else
				$this->data[$key] = $default;
		}
		
		$this->data['data_sites'] = $dev_sites;
		
		$this->data['data_tables'] = $this->db->get_tables();
		
		$this->content();
	}
	
	public function site_management(){
		$this->template->load('dev/site_management');
		
		$this->load->language('dev/dev');
		
		$this->document->setTitle($this->_('text_site_management'));
		
		$dev_sites = $this->model_setting_setting->getSetting('dev_sites');
		
		if($_SERVER["REQUEST_METHOD"] == 'POST' && $this->validate()){
			if(isset($_POST['add_site'])){
				unset($_POST['add_site']);
				$dev_sites[] = $_POST;
			}
			elseif(isset($_POST['delete_site'])){
				foreach($dev_sites as $key => $site){
					if($_POST['domain'] == $site['domain']){
						unset($dev_sites[$key]);
					}
				}
			}
			
			unset($_POST);
			
			$this->model_setting_setting->editSetting('dev_sites', $dev_sites, null, false); 
		}
		
		$this->breadcrumb->add($this->_('text_site_management'), $this->url->link('dev/dev/site_management'));
		
		$defaults = array(
			'domain' => '',
			'username' => '',
			'status' => 'live',
		);

		foreach($defaults as $key=>$default){
			if(isset($_POST[$key])){
				$this->data[$key] = $_POST[$key];
			}
			else{
				$this->data[$key] = $default;
			}
		}
		
		$this->data['dev_sites'] = $dev_sites;
		
		$this->content();
	}
	
	public function backup_restore(){
		$this->template->load('dev/backup_restore');
		
		$this->load->language('dev/dev');
		
		$this->document->setTitle($this->_('text_backup_restore'));
		
		if($_SERVER["REQUEST_METHOD"] == 'POST' && $this->validate()){
			if(isset($_POST['site_backup'])){
				$this->dev->site_backup();
			}
			elseif(isset($_POST['site_restore'])){
				$this->dev->site_restore($_POST['backup_file']);
			}
		}
		
		$this->breadcrumb->add($this->_('text_backup_restore'), $this->url->link('dev/dev/backup_restore'));
		
		$this->data['data_backup_files'] = $this->model_dev_dev->getBackupFiles();
		
		$this->content();
	}
	
	public function content(){
		$this->document->addStyle(HTTP_STYLES . 'dev.css');
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'), '', 0);
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('dev/dev'), '', 1);
		
		$this->data['return'] = $this->url->link('common/home');
		
		
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}
	
	public function request_table_data() {
		$this->language->load('dev/dev');
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['tables']) && $this->validate()) {
			$file = DIR_DOWNLOAD . 'tempsql.sql';
			
			$this->db->dump($_POST['tables'], $file);
			
			include($file);
			
			unlink($file);
		} else {
			echo $this->_('error_sync_table');
		}
		
		exit;
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'dev/dev')) {
			$this->error['warning'] = $this->_('error_permission');
		}
				
		return $this->error ? false : true;
	}
}
