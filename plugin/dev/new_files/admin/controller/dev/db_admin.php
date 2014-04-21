<?php
class Admin_Controller_Dev_DbAdmin extends Controller
{
	public function index()
	{
		if (!$this->user->can('modify', 'dev/dev')) {
			$this->message->add('warning', _l("You do not have permission use the Database Administration Console"));
			$this->url->redirect('common/home');
		}

		//Page Head
		$this->document->setTitle(_l("Database Administration"));
		$this->document->addStyle(URL_THEME . 'style/dev.css');

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'), '', 0);
		$this->breadcrumb->add(_l("Development Console"), $this->url->link('dev/dev'), '', 1);
		$this->breadcrumb->add(_l("Database Administration"), $this->url->link('dev/db_admin'));

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

		$data['return'] = $this->url->link('common/home');

		//Render
		$this->response->setOutput($this->render('dev/db_admin', $data));
	}
}
