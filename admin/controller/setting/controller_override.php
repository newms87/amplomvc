<?php
class Admin_Controller_Setting_ControllerOverride extends Controller
{
	public function index()
	{
		//Template and Language
		$this->template->load('setting/controller_override');
		$this->language->load('setting/controller_override');

		//Page Head
		$this->document->setTitle($this->_('head_title'));

		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_store_list'), $this->url->link('setting/store'));
		$this->breadcrumb->add($this->_('text_settings'), $this->url->link('setting/setting'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('setting/controller_override'));

		//Load Information
		if ($this->request->isPost() && $this->validate()) {
			$controller_overrides = !empty($_POST['controller_overrides']) ? $_POST['controller_overrides'] : array();

			$this->config->save('controller_override', 'controller_override', $controller_overrides, 0, true);

			if (!$this->message->error_set()) {
				$this->message->add('success', $this->_('text_success'));
				$this->url->redirect('setting/setting');
			}
		}

		//Load Data or Defaults
		if (!$this->request->isPost()) {
			$controller_overrides = $this->config->load('controller_override', 'controller_override', 0);
		} else {
			$controller_overrides = $_POST['controller_overrides'];
		}

		if (!$controller_overrides) {
			$controller_overrides = array();
		}

		//Add in the template row
		$controller_overrides['__ac_template__'] = array(
			'original'  => $this->_('entry_original_controller'),
			'alternate' => $this->_('entry_alternate_controller'),
			'condition' => $this->_('entry_condition'),
		);

		$this->data['controller_overrides'] = $controller_overrides;

		//Action Buttons
		$this->data['save']   = $this->url->link('setting/controller_override');
		$this->data['cancel'] = $this->url->link('setting/store');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'localisation/controller_override')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!empty($_POST['controller_overrides'])) {
			foreach ($_POST['controller_overrides'] as $key => $override) {
				if (!is_file(SITE_DIR . $override['original'] . '.php')) {
					$this->error["controller_overrides[$key][original]"] = $this->_('error_original_controller', $override['original']);
				}

				if (!is_file(SITE_DIR . $override['alternate'] . '.php')) {
					$this->error["controller_overrides[$key][alternate]"] = $this->_('error_alternate_controller', $override['alternate']);
				}
			}
		}

		return $this->error ? false : true;
	}
}
