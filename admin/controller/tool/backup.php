<?php 
class ControllerToolBackup extends Controller { 
	
	
	public function index() {		
		$this->template->load('tool/backup');

		$this->load->language('tool/backup');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			if (is_uploaded_file($_FILES['import']['tmp_name'])) {
				$content = file_get_contents($_FILES['import']['tmp_name']);
			} else {
				$content = false;
			}
			
			if ($content) {
				$this->model_tool_backup->restore($content);
				
				$this->message->add('success', $this->_('text_success'));
				
				$this->url->redirect($this->url->link('tool/backup'));
			} else {
				$this->error['warning'] = $this->_('error_empty');
			}
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('tool/backup'));

		$this->data['restore'] = $this->url->link('tool/backup');

		$this->data['backup'] = $this->url->link('tool/backup/backup');

		$this->data['tables'] = $this->model_tool_backup->getTables();

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	public function backup() {
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename=backup.sql');
			$this->response->addheader('Content-Transfer-Encoding: binary');
			
			$this->response->setOutput($this->model_tool_backup->backup($_POST['backup']));
		} else {
			return $this->forward('error/permission');
		}
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'tool/backup')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;		
	}
}