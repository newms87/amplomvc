<?php
class Admin_Controller_Feed_GoogleSitemap extends Controller
{


	public function index()
	{
		$this->view->load('feed/google_sitemap');

		$this->document->setTitle(_l("Google Sitemap"));

		if ($this->request->isPost() && $this->validate()) {
			$this->config->saveGroup('google_sitemap', $_POST);

			$this->message->add('success', _l("Success: You have modified Google Sitemap feed!"));

			$this->url->redirect('extension/feed');
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Product Feeds"), $this->url->link('extension/feed'));
		$this->breadcrumb->add(_l("Google Sitemap"), $this->url->link('feed/google_sitemap'));

		$this->data['action'] = $this->url->link('feed/google_sitemap');

		$this->data['cancel'] = $this->url->link('extension/feed');

		if (isset($_POST['google_sitemap_status'])) {
			$this->data['google_sitemap_status'] = $_POST['google_sitemap_status'];
		} else {
			$this->data['google_sitemap_status'] = $this->config->get('google_sitemap_status');
		}

		$this->data['data_feed'] = $this->url->link('feed/google_sitemap');

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'feed/google_sitemap')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify Google Sitemap feed!");
		}

		return $this->error ? false : true;
	}
}
