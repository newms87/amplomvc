<?php
class App_Controller_Admin_Tool_Tool extends Controller
{
	static $allow = array(
		'modify' => array(
			'clear_cache',
		),
	);

	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("System Tools"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('admin'));
		$this->breadcrumb->add(_l("System Tools"), site_url('admin/tool/tool'));

		$data['clear_cache'] = site_url('admin/tool/tool/clear_cache');

		$data['cancel'] = site_url('admin');

		$defaults = array(
			'cache_tables' => ''
		);

		$data += $defaults;


		$this->response->setOutput($this->render('tool/tool', $data));
	}

	public function clear_cache()
	{
		$tables = !empty($_POST['cache_tables']) ? $_POST['cache_tables'] : '';

		$this->cache->delete($tables);
		$this->message->add('success', _l("The cache table was successfully cleared!"));

		redirect('admin/tool/tool');
	}
}
