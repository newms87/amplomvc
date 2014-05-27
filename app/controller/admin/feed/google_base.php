<?php
class App_Controller_Admin_Feed_GoogleBase extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Google Base"));

		if ($this->request->isPost() && $this->validate()) {
			$this->config->saveGroup('google_base', $_POST);

			$this->message->add('success', _l("Success: You have modified Google Base feed!"));

			redirect('admin/extension/feed');
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Product Feeds"), site_url('admin/extension/feed'));
		$this->breadcrumb->add(_l("Google Base"), site_url('admin/feed/google_base'));

		$data['action'] = site_url('admin/feed/google_base');

		$data['cancel'] = site_url('admin/extension/feed');

		if (isset($_POST['google_base_status'])) {
			$data['google_base_status'] = $_POST['google_base_status'];
		} else {
			$data['google_base_status'] = option('google_base_status');
		}

		$data['data_feed'] = site_url('admin/feed/google_base');

		$this->response->setOutput($this->render('feed/google_base', $data));
	}

	private function validate()
	{
		if (!user_can('modify', 'feed/google_base')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify Google Base feed!");
		}

		return empty($this->error);
	}
}