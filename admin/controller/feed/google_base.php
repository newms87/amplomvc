<?php
class Admin_Controller_Feed_GoogleBase extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Google Base"));

		if ($this->request->isPost() && $this->validate()) {
			$this->config->saveGroup('google_base', $_POST);

			$this->message->add('success', _l("Success: You have modified Google Base feed!"));

			$this->url->redirect('extension/feed');
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Product Feeds"), $this->url->link('extension/feed'));
		$this->breadcrumb->add(_l("Google Base"), $this->url->link('feed/google_base'));

		$data['action'] = $this->url->link('feed/google_base');

		$data['cancel'] = $this->url->link('extension/feed');

		if (isset($_POST['google_base_status'])) {
			$data['google_base_status'] = $_POST['google_base_status'];
		} else {
			$data['google_base_status'] = $this->config->get('google_base_status');
		}

		$data['data_feed'] = $this->url->link('feed/google_base');

		$this->response->setOutput($this->render('feed/google_base', $data));
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'feed/google_base')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify Google Base feed!");
		}

		return $this->error ? false : true;
	}
}
