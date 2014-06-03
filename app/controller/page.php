<?php

class App_Controller_Page extends Controller
{
	public function index()
	{
		//The page
		$page_id = _get('page_id', 0);

		if ($page_id) {
			$page = $this->Model_Page->getActivePage($page_id);
		} else {
			$page = $this->Model_Page->getPageByName($this->route->getSegment(1));
		}

		if (!$page) {
			redirect("error/not_found");
		}

		//Page Head
		$this->document->setTitle($page['title']);

		if ($page['style']) {
			if (pathinfo($page['style'], PATHINFO_EXTENSION) === 'less') {
				$style = $this->document->compileLess($page['style'], 'page-' . $page['name']);
			} else {
				$style = $page['style'];
			}

			$this->document->addStyle($style);
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add($page['title'], $this->url->here());

		//Change Layout to desired page layout
		$this->config->set('config_layout_id', $page['layout_id']);

		$template = !empty($page['template']) ? 'page/' . $page['template'] : 'page/default';

		//Render
		$this->response->setOutput($this->render($template, $page));
	}

	public function content()
	{
		//The page
		$page_id = _get('page_id', 0);

		$page = $this->Model_Page->getActivePage($page_id);

		if (!$page) {
			redirect("error/not_found");
		}

		if ($page['style']) {
			if (pathinfo($page['style'], PATHINFO_EXTENSION) === 'less') {
				$page['style'] = str_replace(URL_SITE, DIR_SITE, $this->document->compileLess($page['style'], 'page-' . $page['name']));
			}
		}

		//Render
		$this->response->setOutput($this->render('page/content', $page));
	}

	public function preview()
	{
		//The page
		$page_id = _get('page_id');

		if ($page_id) {
			$page = $this->Model_Page->getPageForPreview($page_id);
		} else {
			$page = array(
				'page_id'       => 0,
				'name'          => 'new-page',
				'title'         => "New Page",
				'display_title' => 1,
				'content'       => '',
				'style'         => '',
				'layout_id'     => option('config_default_layout'),
			);
		}

		if (!$page) {
			redirect("error/not_found");
		}

		//Page Head
		$this->document->setTitle($page['title']);

		if ($page['style']) {
			if (pathinfo($page['style'], PATHINFO_EXTENSION) === 'less') {
				$page['style'] = str_replace(URL_SITE, DIR_SITE, $this->document->compileLess($page['style'], 'page-' . $page['name']));
			}
		}

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add($page['title'], $this->url->here());

		//Change Layout to desired page layout
		$this->config->set('config_layout_id', $page['layout_id']);

		$page['template'] = !empty($page['template']) ? 'page/' . $page['template'] : 'page/default';

		//Render
		$this->response->setOutput($this->render('page/preview', $page));
	}
}
