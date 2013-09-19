<?php
class Admin_Controller_Extension_Shipping extends Controller
{
	public function index()
	{
		$this->template->load('extension/shipping');

		$this->language->load('extension/shipping');

		$this->document->setTitle($this->_('head_title'));

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('extension/shipping'));

		$extensions = $this->Model_Setting_Extension->getInstalled('shipping');

		foreach ($extensions as $key => $value) {
			if (!file_exists(DIR_APPLICATION . 'controller/shipping/' . $value . '.php')) {
				$this->Model_Setting_Extension->uninstall('shipping', $value);

				unset($extensions[$key]);
			}
		}

		$this->data['extensions'] = array();

		$files = glob(DIR_APPLICATION . 'controller/shipping/*.php');

		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');

				$this->language->load('shipping/' . $extension);

				$action = array();

				if (!in_array($extension, $extensions)) {
					$action[] = array(
						'text' => $this->_('text_install'),
						'href' => $this->url->link('extension/shipping/install', 'extension=' . $extension)
					);
				} else {
					$action[] = array(
						'text' => $this->_('text_edit'),
						'href' => $this->url->link('shipping/' . $extension . '')
					);

					$action[] = array(
						'text' => $this->_('text_uninstall'),
						'href' => $this->url->link('extension/shipping/uninstall', 'extension=' . $extension)
					);
				}

				$this->data['extensions'][] = array(
					'name'       => $this->_('head_title'),
					'status'     => $this->config->get($extension . '_status') ? $this->_('text_enabled') : $this->_('text_disabled'),
					'sort_order' => $this->config->get($extension . '_sort_order'),
					'action'     => $action
				);
			}
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function install()
	{
		if (!$this->user->hasPermission('modify', 'extension/shipping')) {
			$this->session->data['error'] = $this->_('error_permission');

			$this->url->redirect($this->url->link('extension/shipping'));
		} else {
			$this->Model_Setting_Extension->install('shipping', $_GET['extension']);

			$this->Model_User_UserGroup->addPermission($this->user->getId(), 'access', 'shipping/' . $_GET['extension']);
			$this->Model_User_UserGroup->addPermission($this->user->getId(), 'modify', 'shipping/' . $_GET['extension']);

			require_once(_ac_mod_file(DIR_APPLICATION . 'controller/shipping/' . $_GET['extension'] . '.php'));

			$class = 'Admin_Controller_Shipping_' . $this->tool->formatClassname($_GET['extension']);
			$class = new $class($this->registry);

			if (method_exists($class, 'install')) {
				$class->install();
			}

			$this->url->redirect($this->url->link('extension/shipping'));
		}
	}

	public function uninstall()
	{
		if (!$this->user->hasPermission('modify', 'extension/shipping')) {
			$this->session->data['error'] = $this->_('error_permission');

			$this->url->redirect($this->url->link('extension/shipping'));
		} else {
			$this->Model_Setting_Extension->uninstall('shipping', $_GET['extension']);

			$this->System_Model_Setting->deleteSetting($_GET['extension']);

			require_once(_ac_mod_file(DIR_APPLICATION . 'controller/shipping/' . $_GET['extension'] . '.php'));

			$class = 'ControllerShipping' . str_replace('_', '', $_GET['extension']);
			$class = new $class($this->registry);

			if (method_exists($class, 'uninstall')) {
				$class->uninstall();
			}

			$this->url->redirect($this->url->link('extension/shipping'));
		}
	}
}
