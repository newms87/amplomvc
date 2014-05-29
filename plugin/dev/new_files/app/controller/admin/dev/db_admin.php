<?php
class App_Controller_Admin_Dev_DbAdmin extends Controller
{
	public function index()
	{
		if (!user_can('modify', 'dev/dev')) {
			$this->message->add('warning', _l("You do not have permission use the Database Administration Console"));
			redirect('admin/common/home');
		}

		//Page Head
		$this->document->setTitle(_l("Database Administration"));
		$this->document->addStyle(URL_THEME . 'style/dev.css');

		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'), '', 0);
		$this->breadcrumb->add(_l("Development Console"), site_url('dev/dev'), '', 1);
		$this->breadcrumb->add(_l("Database Administration"), site_url('dev/db_admin'));

		$data = array();

		//Check for post data
		if ($this->request->isPost()) {
			if (!empty($_POST['query'])) {
				$results = $this->db->queryRows(html_entity_decode($_POST['query'], ENT_QUOTES, 'UTF-8'));

				$data['results'] = $results;
			}
		}

		$defaults = array(
			'query' => '',
		);

		$data += $_POST + $defaults;

		$data['data_tables'] = $this->db->getTables();

		$data['return'] = site_url('admin/common/home');

		//Render
		$this->response->setOutput($this->render('dev/db_admin', $data));
	}
}
