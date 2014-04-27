<?php
class Admin_Controller_Feed_GoogleSitemap extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Google Sitemap"));

		if ($this->request->isPost() && $this->validate()) {
			$this->config->saveGroup('google_sitemap', $_POST);

			$this->message->add('success', _l("Success: You have modified Google Sitemap feed!"));

			$this->url->redirect('extension/feed');
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Product Feeds"), $this->url->link('extension/feed'));
		$this->breadcrumb->add(_l("Google Sitemap"), $this->url->link('feed/google_sitemap'));

		$data['action'] = $this->url->link('feed/google_sitemap');

		$data['cancel'] = $this->url->link('extension/feed');

		if (isset($_POST['google_sitemap_status'])) {
			$data['google_sitemap_status'] = $_POST['google_sitemap_status'];
		} else {
			$data['google_sitemap_status'] = $this->config->get('google_sitemap_status');
		}

		$data['data_feed'] = $this->url->link('feed/google_sitemap');

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
