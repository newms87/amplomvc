<?php
class Admin_Controller_Feed_GoogleSitemap extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Google Sitemap"));

		if ($this->request->isPost() && $this->validate()) {
			$this->config->saveGroup('google_sitemap', $_POST);

			$this->message->add('success', _l("Success: You have modified Google Sitemap feed!"));

			redirect('extension/feed');
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Product Feeds"), site_url('extension/feed'));
		$this->breadcrumb->add(_l("Google Sitemap"), site_url('feed/google_sitemap'));

		$data['action'] = site_url('feed/google_sitemap');

		$data['cancel'] = site_url('extension/feed');

		if (isset($_POST['google_sitemap_status'])) {
			$data['google_sitemap_status'] = $_POST['google_sitemap_status'];
		} else {
			$data['google_sitemap_status'] = $this->config->get('google_sitemap_status');
		}

		$data['data_feed'] = site_url('feed/google_sitemap');

		$this->response->setOutput($this->render('feed/google_sitemap', $data));
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'feed/google_sitemap')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify Google Sitemap feed!");
		}

		return empty($this->error);
	}
}
