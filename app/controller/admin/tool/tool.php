<?php
class App_Controller_Admin_Tool_Tool extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("System Tools"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("System Tools"), site_url('admin/tool/tool'));

		$data['clear_cache'] = site_url('admin/tool/tool/clear_cache');

		$data['cancel'] = site_url('admin');

		$defaults = array(
			'cache_tables' => ''
		);

		$data += $defaults;


		output($this->render('tool/tool', $data));
	}

	public function clear_cache()
	{
		$tables = !empty($_POST['cache_tables']) ? $_POST['cache_tables'] : '';

		$this->cache->delete($tables);
		message('success', _l("The cache table was successfully cleared!"));

		redirect('admin/tool/tool');
	}
}
