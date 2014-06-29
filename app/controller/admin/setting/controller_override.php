<?php
class App_Controller_Admin_Setting_ControllerOverride extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Controller Override"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("Stores"), site_url('admin/setting/store'));
		$this->breadcrumb->add(_l("Settings"), site_url('admin/setting/setting'));
		$this->breadcrumb->add(_l("Controller Override"), site_url('admin/setting/controller_override'));

		//Load Information
		if (is_post() && $this->validate()) {
			$controller_overrides = !empty($_POST['controller_overrides']) ? $_POST['controller_overrides'] : array();

			$this->config->save('controller_override', 'controller_override', $controller_overrides, 0, true);

			if (!$this->message->has('error', 'warning')) {
				message('success', _l("You have successfully updated the Controller Overrides"));
				redirect('admin/setting/setting');
			}
		}

		//Load Data or Defaults
		if (!is_post()) {
			$controller_overrides = $this->config->load('controller_override', 'controller_override', 0);
		} else {
			$controller_overrides = $_POST['controller_overrides'];
		}

		if (!$controller_overrides) {
			$controller_overrides = array();
		}

		//Add in the template row
		$controller_overrides['__ac_template__'] = array(
			'original'  => _l("original/controller/path"),
			'alternate' => _l("alternate/controller/path"),
			'condition' => _l("Query Condition Regular Expression (eg: 'product_id=*')"),
		);

		$data['controller_overrides'] = $controller_overrides;

		//Action Buttons
		$data['save']   = site_url('admin/setting/controller_override');
		$data['cancel'] = site_url('admin/setting/store');

		//Render
		output($this->render('setting/controller_override', $data));
	}

	private function validate()
	{
		if (!user_can('modify', 'localisation/controller_override')) {
			$this->error['warning'] = _l("You do not have permission to modify Controller Overrides");
		}

		if (!empty($_POST['controller_overrides'])) {
			foreach ($_POST['controller_overrides'] as $key => $override) {
				if (!is_file(DIR_SITE . $override['original'] . '.php')) {
					$this->error["controller_overrides[$key][original]"] = _l("The Original Controller at %s did not exist!", $override['original']);
				}

				if (!is_file(DIR_SITE . $override['alternate'] . '.php')) {
					$this->error["controller_overrides[$key][alternate]"] = _l("The Alternate Controller at %s did not exist!", $override['alternate']);
				}
			}
		}

		return empty($this->error);
	}
}
