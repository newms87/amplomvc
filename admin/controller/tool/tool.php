<?php
class Admin_Controller_Tool_Tool extends Controller
{
	public function index()
	{
		$this->template->load('tool/tool');

		$this->language->load('tool/tool');

		$this->document->setTitle(_l("System Tools"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("System Tools"), $this->url->link('tool/tool'));

		$this->data['clear_cache'] = $this->url->link('tool/tool/clear_cache');

		$this->data['cancel'] = $this->url->link('common/home');

		$defaults = array(
			'cache_tables' => ''
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} else {
				$this->data[$key] = $default;
			}
		}


		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function clear_cache()
	{
		$this->language->load('tool/tool');

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

		return $this->error ? false : true;
	}
}
