<?php
class App_Controller_Admin_Tool_Tool extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("System Tools"));

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("System Tools"), site_url('tool/tool'));

		$data['clear_cache'] = site_url('tool/tool/clear_cache');

		$data['cancel'] = site_url('common/home');

		$defaults = array(
			'cache_tables' => ''
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$data[$key] = $_POST[$key];
			} else {
				$data[$key] = $default;
			}
		}


		$this->response->setOutput($this->render('tool/tool', $data));
	}

	public function clear_cache()
	{
		if ($this->request->isPost() && isset($_POST['cache_tables']) && $this->validate()) {

			$this->cache->delete($_POST['cache_tables']);

			$this->message->add('success', _l("The cache table was successfully cleared!"));
		} else {
			$this->message->add('warning', _l("Unable to clear the cache table!"));
		}

		$this->index();
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'tool/tool')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify tools!");
		}

		return empty($this->error);
	}
}
