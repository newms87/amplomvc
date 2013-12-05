<?php
class Admin_Controller_Tool_Backup extends Controller
{

	//TODO: Probably dont need this anymore...

	public function index()
	{
		$this->template->load('tool/backup');

		$this->language->load('tool/backup');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validate()) {
			if (is_uploaded_file($_FILES['import']['tmp_name'])) {
				$content = file_get_contents($_FILES['import']['tmp_name']);
			} else {
				$content = false;
			}

			if ($content) {
				$this->Model_Tool_Backup->restore($content);

				$this->message->add('success', $this->_('text_success'));

				$this->url->redirect('tool/backup');
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
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('tool/backup'));

		$this->data['restore'] = $this->url->link('tool/backup');

		$this->data['backup'] = $this->url->link('tool/backup/backup');

		$this->data['tables'] = $this->Model_Tool_Backup->getTables();

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function backup()
	{
		if ($this->request->isPost() && $this->validate()) {
			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename=backup.sql');
			$this->response->addheader('Content-Transfer-Encoding: binary');

			$this->response->setOutput($this->Model_Tool_Backup->backup($_POST['backup']));
		} else {
			//TODO if we do not remove this, lets change how we handle this...
			//return $this->forward('error/permission');
			trigger_error("Unable to backup server. User could not be verified");
			exit;
		}
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'tool/backup')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
	}
}
