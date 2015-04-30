<?php

class App_Controller_Page extends Controller
{
	public function index()
	{
		//The page
		$page_id = _get('page_id');

		if ($page_id) {
			$page = $this->Model_Page->getActivePage($page_id);
		} else {
			$page = $this->Model_Page->getPageByName($this->route->getSegment(1));
		}

		if (!$page) {
			return call('error/not_found');
		}

		$page_id = $page['page_id'];

		//Page Head
		set_page_info('title', $page['title']);

		//TODO: Put the page style into a cached file. (load in page header!)
		$page['style'] = $this->Model_Page->compileStyle($page_id, $page['style']);

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb($page['title'], $this->url->here());

		//Change Layout to desired page layout
		set_option('config_layout_id', $page['layout_id']);

		$page['content_file'] = _mod($page['content_file']);

		$template = 'page/template/' . (!empty($page['template']) ? $page['template'] : 'default');

		//Render
		output($this->render($template, $page));
	}

	public function preview($page = array())
	{
		//The page
		$page_id = _get('page_id');

		if ($page_id) {
			$page += $this->Model_Page->getPageForPreview($page_id);
		} else {
			$page += array(
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
			return call('error/not_found');
		}

		//Page Head
		set_page_info('title', $page['title']);

		if ($page['style']) {
			$page['style'] = $this->document->compileLessContent($page['style']);
		}

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb($page['title'], $this->url->here());

		//Change Layout to desired page layout
		set_option('config_layout_id', $page['layout_id']);

		$template = 'page/template/' . (!empty($page['template']) ? $page['template'] : 'default');

		//Render
		output($this->render($template, $page));
	}

	public function preview_content($page = array())
	{
		//The page
		$page_id = _get('page_id', 0);

		$page += $this->Model_Page->getPageForPreview($page_id);

		if (IS_POST) {
			$page['content'] = html_entity_decode(_post('content'));
			$page['style']   = html_entity_decode(_post('style'));
		}

		if (!$page) {
			return '';
		}

		if ($page['style']) {
			$page['style'] = $this->document->compileLessContent($page['style']);
		}

		//Render
		output($this->render('page/template/default', $page));
	}
}
