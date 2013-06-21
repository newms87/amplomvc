<?php
class Admin_Controller_Module_Cron extends Controller 
{
	
	public function index()
	{
		$this->load->language('module/cron');
			
		$this->template->load('module/cron');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {

			$this->Model_Setting_Setting->editSetting('cron_tasks', $_POST);
			
			
			//TODO: Implement full cron control from this code:
			/*
			* 		$output = shell_exec('crontab -l');
		echo $output;
		file_put_contents('/tmp/crontab.txt', $output.'* * * * * NEW_CRON'.PHP_EOL);
		echo exec('crontab /tmp/crontab.txt');
		echo exec('rm -fv /tmp/crontab.txt');
			*/
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('module/cron'));
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('module/cron'));
		
		$this->data['action'] = $this->url->link('module/cron');
		$this->data['cancel'] = $this->url->link('extension/module');

		$this->data['run_cron'] = $this->url->store(null, 'cron/cron');
		
		$info = isset($_POST['cron_tasks'])?$_POST['cron_tasks']:$this->Model_Setting_Setting->getSetting('cron_tasks');
		$this->data['tasks'] = isset($info['tasks']) ? $info['tasks'] : array();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/cron')) {
			$this->error['warning'] = $this->_('error_permission');
		}
				
		return $this->error ? false : true;
	}
}
