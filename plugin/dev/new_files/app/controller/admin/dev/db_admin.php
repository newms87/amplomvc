<?php
class App_Controller_Admin_Dev_DbAdmin extends Controller
{
	public function index()
	{
		if (!user_can('modify', 'dev/dev')) {
			message('warning', _l("You do not have permission use the Database Administration Console"));
			redirect();
		}

		//Page Head
		$this->document->setTitle(_l("Database Administration"));
		$this->document->addStyle(URL_THEME . 'style/dev.css');

		$this->breadcrumb->add(_l("Home"), site_url('admin'), '', 0);
		$this->breadcrumb->add(_l("Development Console"), site_url('dev/dev'), '', 1);
		$this->breadcrumb->add(_l("Database Administration"), site_url('dev/db_admin'));

		$data = array();

		//Check for post data
		if (is_post()) {
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

		$data['return'] = site_url('admin');

		//Render
		output($this->render('dev/db_admin', $data));
	}
}
