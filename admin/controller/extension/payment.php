<?php
class ControllerExtensionPayment extends Controller {
	public function index() {
		$this->template->load('extension/payment');

		$this->load->language('extension/payment');
		
		$this->document->setTitle($this->_('heading_title'));

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('extension/payment'));

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if (isset($this->session->data['error'])) {
			$this->data['error'] = $this->session->data['error'];
		
			unset($this->session->data['error']);
		} else {
			$this->data['error'] = '';
		}

		$extensions = $this->model_setting_extension->getInstalled('payment');
		
		foreach ($extensions as $key => $value) {
			if (!file_exists(DIR_APPLICATION . 'controller/payment/' . $value . '.php')) {
				$this->model_setting_extension->uninstall('payment', $value);
				
				unset($extensions[$key]);
			}
		}
		
		$this->data['extensions'] = array();
						
		$files = glob(DIR_APPLICATION . 'controller/payment/*.php');
		
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				
				$this->load->language('payment/' . $extension);
	
				$action = array();
				
				if (!in_array($extension, $extensions)) {
					$action[] = array(
						'text' => $this->_('text_install'),
						'href' => $this->url->link('extension/payment/install', 'extension=' . $extension)
					);
				} else {
					$action[] = array(
						'text' => $this->_('text_edit'),
						'href' => $this->url->link('payment/' . $extension . '')
					);
								
					$action[] = array(
						'text' => $this->_('text_uninstall'),
						'href' => $this->url->link('extension/payment/uninstall', 'extension=' . $extension)
					);
				}
				
				$text_link = $this->_('text_' . $extension);
				
				if ($text_link != 'text_' . $extension) {
					$link = $this->_('text_' . $extension);
				} else {
					$link = '';
				}
				
				$this->data['extensions'][] = array(
					'name'		=> $this->_('heading_title'),
					'link'		=> $link,
					'status'	=> $this->config->get($extension . '_status') ? $this->_('text_enabled') : $this->_('text_disabled'),
					'sort_order' => $this->config->get($extension . '_sort_order'),
					'action'	=> $action
				);
			}
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	public function install() {
		if (!$this->user->hasPermission('modify', 'extension/payment')) {
			$this->session->data['error'] = $this->_('error_permission');
			
			$this->url->redirect($this->url->link('extension/payment'));
		} else {
			$this->model_setting_extension->install('payment', $_GET['extension']);

			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'payment/' . $_GET['extension']);
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'payment/' . $_GET['extension']);

			_require_once(DIR_APPLICATION . 'controller/payment/' . $_GET['extension'] . '.php');
			
			$class = 'ControllerPayment' . str_replace('_', '', $_GET['extension']);
			$class = new $class($this->registry);
			
			if (method_exists($class, 'install')) {
				$class->install();
			}
			
			$this->url->redirect($this->url->link('extension/payment'));
		}
	}
	
	public function uninstall() {
		if (!$this->user->hasPermission('modify', 'extension/payment')) {
			$this->session->data['error'] = $this->_('error_permission');
			
			$this->url->redirect($this->url->link('extension/payment'));
		} else {
			$this->model_setting_extension->uninstall('payment', $_GET['extension']);
		
			$this->model_setting_setting->deleteSetting($_GET['extension']);
		
			_require_once(DIR_APPLICATION . 'controller/payment/' . $_GET['extension'] . '.php');
			
			$class = 'ControllerPayment' . str_replace('_', '', $_GET['extension']);
			$class = new $class($this->registry);
			
			if (method_exists($class, 'uninstall')) {
				$class->uninstall();
			}
		
			$this->url->redirect($this->url->link('extension/payment'));
		}
	}
}