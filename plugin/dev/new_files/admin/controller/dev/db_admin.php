<?php
class Admin_Controller_Dev_DbAdmin extends Controller
{
	public function index()
	{
		$this->template->load('dev/db_admin');
		$this->document->setTitle(_l("Database Administration"));

		$this->document->addStyle(HTTP_THEME_STYLE . 'dev.css');

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'), '', 0);
		$this->breadcrumb->add(_l("Development Console"), $this->url->link('dev/dev'), '', 1);
		$this->breadcrumb->add(_l("Database Administration"), $this->url->link('dev/db_admin'));

		$this->data['return'] = $this->url->link('common/home');

		//Check for post data
		if ($this->request->isPost() && $this->validate()) {
			if (!empty($_POST['query'])) {
				$results = $this->db->queryRows(html_entity_decode($_POST['query'], ENT_QUOTES, 'UTF-8'));

				$this->data['results'] = $results;
			}
		}

		$defaults = array(
			'query' => '',
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		$this->data['data_tables'] = $this->db->getTables();

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function validate()
	{
		if (!$this->user->can('modify', 'dev/dev')) {
			$this->error['warning'] = _l("You do not have permission use the Database Administration Console");
		}

		return $this->error ? false : true;
	}
}
