<?php

class App_Model_Page extends Model
{
	private $page_theme;

	public function __construct()
	{
		parent::__construct();
		$this->page_theme = option('config_theme');

		if ($this->page_theme === 'admin') {
			$this->page_theme = 'fluid';
		}

		$this->loadPages();
	}

	public function addPage($page)
	{
		//Defaults
		$page += array(
			'content' => '',
			'style'   => '',
		);

		if (!validate('text', $page['title'], 1, 64)) {
			$this->error['title'] = _l("Page Title must be between 1 and 64 characters!");
		}

		if ($this->error) {
			return false;
		}

		$page_id = $this->insert('page', $page);

		$dir = DIR_THEMES . option('config_default_theme', 'fluid') . '/template/page/' . $page['name'];

		file_put_contents($dir . '/content.tpl', $page['content']);
		file_put_contents($dir . '/style.less', $page['style']);

		if (!empty($page['stores'])) {
			foreach ($page['stores'] as $store) {
				if (is_array($store)) {
					$layout_id = $store['layout_id'];
					$store_id  = $store['store_id'];
				} else {
					$store_id  = $store;
					$layout_id = option('config_default_layout_id');
				}

				$store_data = array(
					'page_id'   => $page_id,
					'store_id'  => $store_id,
					'layout_id' => $layout_id,
				);

				$this->insert('page_store', $store_data);
			}
		}

		if (!empty($page['alias'])) {
			$this->url->setAlias($page['alias'], 'page/page', 'page_id=' . (int)$page_id);
		}

		if (!empty($page['translations'])) {
			$this->translation->setTranslations('page', $page_id, $page['translations']);
		}

		$this->cache->delete('page');

		return $page_id;
	}

	public function editPage($page_id, $page)
	{
		if (isset($page['title']) && !validate('text', $page['title'], 3, 64)) {
			$this->error['title'] = _l("Page Title must be between 3 and 64 characters!");
		}

		if ($this->error) {
			return false;
		}

		$this->update('page', $page, $page_id);

		$page['name'] = $this->queryVar("SELECT name FROM " . $this->prefix . "page WHERE page_id = " . (int)$page_id);

		$dir = DIR_THEMES . option('config_default_theme', 'fluid') . '/template/page/' . $page['name'];

		if (isset($page['content'])) {
			file_put_contents($dir . '/content.tpl', $page['content']);
		}

		if (isset($page['style'])) {
			file_put_contents($dir . '/style.less', $page['style']);
		}

		if (isset($page['stores'])) {
			$this->delete('page_store', array('page_id' => $page_id));

			foreach ($page['stores'] as $store) {
				if (is_array($store)) {
					$layout_id = $store['layout_id'];
					$store_id  = $store['store_id'];
				} else {
					$store_id  = $store;
					$layout_id = option('config_default_layout_id');
				}

				$store_data = array(
					'page_id'   => $page_id,
					'store_id'  => $store_id,
					'layout_id' => $layout_id,
				);

				$this->insert('page_store', $store_data);
			}
		}

		if (isset($page['alias'])) {
			$this->url->setAlias($page['alias'], 'page/page', 'page_id=' . (int)$page_id);
		}

		if (isset($page['translations'])) {
			$this->translation->setTranslations('page', $page_id, $page['translations']);
		}

		$this->cache->delete('page');

		return $page_id;
	}

	public function copyPage($page_id)
	{
		$page            = $this->getPage($page_id);
		$page['stores']  = $this->getPageStores($page_id);
		$page['content'] = $this->getPageContent($page['name']);
		$page['style']   = $this->getPageStyle($page['name']);

		$this->addPage($page);
	}

	public function deletePage($page_id)
	{
		$this->delete('page', $page_id);
		$this->delete('page_store', array('page_id' => $page_id));

		$this->url->removeAlias('page/page', 'page_id=' . $page_id);

		$this->translation->deleteTranslation('page', $page_id);

		$this->cache->delete('page');
	}

	public function getPage($page_id)
	{
		$page = $this->queryRow("SELECT * FROM " . DB_PREFIX . "page WHERE page_id = '" . (int)$page_id . "'");

		$page['content'] = $this->getPageContent($page['name']);
		$page['style']   = $this->getPageStyle($page['name']);

		$page['alias'] = $this->url->getAlias('page/page', 'page_id=' . (int)$page_id);

		//Translations
		$translate_fields = array(
			'title',
			'meta_keywords',
			'meta_description',
			'content',
		);

		$page['translations'] = $this->translation->getTranslations('page', $page_id, $translate_fields);

		return $page;
	}

	//TODO: Develop good caching method for pages.
	public function getActivePage($page_id)
	{
		$store_id = option('store_id');

		$query =
			"SELECT * FROM " . DB_PREFIX . "page p" .
			" LEFT JOIN " . DB_PREFIX . "page_store ps ON (ps.page_id = p.page_id)" .
			" WHERE p.page_id = " . (int)$page_id . " AND p.status = 1 AND ps.store_id = " . (int)$store_id;

		$page = $this->queryRow($query);

		if ($page) {
			$page['content'] = $this->getPageContent($page['name']);
			$page['style']   = $this->getPageStyle($page['name']);
		}

		return $page;
	}

	public function getPageByName($name)
	{
		$store_id = option('store_id');

		$query =
			"SELECT * FROM " . DB_PREFIX . "page p" .
			" LEFT JOIN " . DB_PREFIX . "page_store ps ON (ps.page_id = p.page_id)" .
			" WHERE p.name = '" . $this->escape($name) . "' AND p.status = 1 AND ps.store_id = " . (int)$store_id;

		$page = $this->queryRow($query);

		$file = $this->theme->findFile('page/' . $name . '/content');

		//Page Does Not Exist, but found in database
		if ($page && !$file) {
			$this->deletePage($page['page_id']);
			$page = array();
		}

		if ($page) {
			$page['content'] = $this->getPageContent($page['name']);
			$page['style']   = $this->getPageStyle($page['name']);
		} elseif ($file) {
			if (!$this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "page WHERE `name` = '" . $this->escape($name) . "'")) {
				$page = array(
					'name'          => $name,
					'title'         => ucfirst($name),
					'layout_id'     => option('config_layout_id'),
					'content'       => file_get_contents(URL_THEMES . $file),
					'status'        => 1,
					'display_title' => 1,
					'cache'         => 1,
					'stores'        => array(
						option('store_id'),
					),
				);

				$this->addPage($page);
			}
		}

		return $page;
	}

	public function getPageForPreview($page_id)
	{
		$page = $this->queryRow("SELECT * FROM " . DB_PREFIX . "page WHERE page_id = " . (int)$page_id);

		if ($page) {
			$page['content'] = $this->getPageContent($page['name']);
			$page['style']   = $this->getPageStyle($page['name']);

			$this->translation->translate('page', $page_id, $page);
		}

		return $page;
	}

	public function getPages($filter = array(), $select = '*', $index = null)
	{
		//Select
		if ($index === false) {
			$select = 'COUNT(*)';
		}

		//From
		$from = DB_PREFIX . "page p";

		//Where
		$columns = array(
			'title'  => 'text_like',
			'status' => 'int_equals',
		);

		$where = $this->extractFilter($columns, $filter);

		if (!empty($filter['stores'])) {
			$store_ids = is_array($filter['stores']) ? $filter['stores'] : array($filter['stores']);

			$from .= " LEFT JOIN " . DB_PREFIX . "page_store ps ON (p.page_id=ps.page_id)";

			$where .= " AND ps.store_id IN (" . implode(',', $store_ids) . ")";
		}

		//Order By & Limit
		if (!$index) {
			$order = $this->extractOrder($filter);
			$limit = $this->extractLimit($filter);
		} else {
			$order = '';
			$limit = '';
		}

		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";

		//Get Total
		if ($index === false) {
			return $this->queryVar($query);
		}

		//Get Rows
		$pages = $this->queryRows($query, $index);

		foreach ($pages as &$page) {
			$page['content'] = $this->getPageContent($page['name']);
			$page['style']   = $this->getPageStyle($page['name']);
		}
		unset($row);

		return $pages;
	}

	public function getPageContent($name)
	{
		$content = $this->theme->getFile('page/' . $name . '/content', $this->page_theme);

		if (!$content) {
			trigger_error(_l("The page %s content file was not found. Add page/%s/content.tpl to your theme", $name, $name));

			$page_id = $this->queryVar("SELECT page_id FROM " . DB_PREFIX . "page WHERE `name` = '" . $this->escape($name) . "'");

			if ($page_id) {
				$this->delete('page', $page_id);
			}
		}

		return $content;
	}

	public function getPageStyle($name)
	{
		$style = $this->theme->getFile('page/' . $name . '/style.less', $this->page_theme);

		if (!$style) {
			$style = $this->theme->getFile('page/' . $name . '/style.css', $this->page_theme);
		}

		return $style;
	}

	public function getPageStores($page_id)
	{
		return $this->queryRows("SELECT * FROM " . DB_PREFIX . "page_store WHERE page_id = '" . (int)$page_id . "'");
	}

	public function getTotalPages($data = array())
	{
		return $this->getPages($data, '*', false);
	}

	public function loadPages()
	{
		$pages = $this->cache->get('page.loaded');

		if (is_null($pages)) {
			$pages = $this->getPages(array(), '*', 'name');

			$this->cache->set('page.loaded', $pages);
		}

		clearstatcache();

		$handle = opendir(DIR_THEMES);

		while (($file = readdir($handle)) !== false) {
			if ($file === '.' || $file === '..' || $file === 'admin') {
				continue;
			}

			if (filetype(DIR_THEMES . $file) === 'dir') {
				$page_dir = DIR_THEMES . $file . '/template/page/';

				if ($th = @opendir($page_dir)) {
					while (($name = readdir($th)) !== false) {
						if ($name === '.' || $name === '..') {
							continue;
						}

						if (filetype($page_dir . $name) === 'dir') {
							if (!isset($pages[$name])) {
								$title = array_map(function ($a) { return ucfirst($a); }, explode('_', $name));
								$title = implode(' ', $title);

								$page = array(
									'name'          => $name,
									'title'         => $title,
									'template'      => '',
									'status'        => 1,
									'display_title' => 1,
									'cache'         => 1,
								);

								$this->addPage($page);
							}
						}
					}
				}
			}
		}

		closedir($handle);

	}
}
