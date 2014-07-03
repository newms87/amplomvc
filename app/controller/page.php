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

		if (isset($_GET['content'])) {
			return $this->content($page);
		}

		if (!$page) {
			return call('error/not_found');
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
		breadcrumb(_l("Home"), site_url());
		breadcrumb($page['title'], $this->url->here());

		//Change Layout to desired page layout
		$this->config->set('config_layout_id', $page['layout_id']);

		$template = !empty($page['template']) ? 'page/' . $page['template'] : 'page/default';

		//Render
		output($this->render($template, $page));
	}

	public function content($page = array())
	{
		//The page
		$page_id = _get('page_id', 0);

		if ($page_id) {
			$page += $this->Model_Page->getActivePage($page_id);
		}

		if (!$page) {
			return '';
		}

		if ($page['style']) {
			if (pathinfo($page['style'], PATHINFO_EXTENSION) === 'less') {
				$page['style'] = str_replace(URL_SITE, DIR_SITE, $this->document->compileLess($page['style'], 'page-' . $page['name']));
			}
		}

		//Render
		output($this->render('page/content', $page));
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
		$this->document->setTitle($page['title']);

		if ($page['style']) {
			if (pathinfo($page['style'], PATHINFO_EXTENSION) === 'less') {
				$page['style'] = str_replace(URL_SITE, DIR_SITE, $this->document->compileLess($page['style'], 'page-' . $page['name']));
			}
		}

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url());
		breadcrumb($page['title'], $this->url->here());

		//Change Layout to desired page layout
		$this->config->set('config_layout_id', $page['layout_id']);

		$template = !empty($page['template']) ? 'page/' . $page['template'] : 'page/default';

		//Render
		output($this->render($template, $page));
	}

	public function preview_content($page = array())
	{
		//The page
		$page_id = _get('page_id', 0);

		$page += $this->Model_Page->getPageForPreview($page_id);

		if (IS_POST) {
			$temp_content = DIR_CACHE . 'preview/' . $page['name'] . '/content.tpl';
			$temp_style   = DIR_CACHE . 'preview/' . $page['name'] . '/style.less';

			_is_writable(dirname($temp_content));

			file_put_contents($temp_content, html_entity_decode($_POST['content']));
			file_put_contents($temp_style, html_entity_decode($_POST['style']));

			$page['content'] = $temp_content;
			$page['style'] = $temp_style;
		}

		if (!$page) {
			return '';
		}

		if ($page['style']) {
			if (pathinfo($page['style'], PATHINFO_EXTENSION) === 'less') {
				$page['style'] = str_replace(URL_SITE, DIR_SITE, $this->document->compileLess($page['style'], 'page-' . $page['name']));
			}
		}

		//Render
		if (IS_POST) {
			output($this->render('page/content', $page));
		} else {
			output($this->render('page/default', $page));
		}
	}
}
