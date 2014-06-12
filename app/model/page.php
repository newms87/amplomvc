<?php

class App_Model_Page extends Model
{
	public function __construct()
	{
		parent::__construct();

		$this->loadPages();
	}

	public function addPage($page)
	{
		//Defaults
		$page += array(
			'title'   => '',
			'content' => '',
			'style'   => '',
			'theme'   => option('config_default_theme', 'fluid'),
		);

		if (!validate('text', $page['title'], 1, 64)) {
			$this->error['title'] = _l("Page Title must be between 1 and 64 characters!");
		}

		if ($this->error) {
			return false;
		}

		if (empty($page['name'])) {
			$page['name'] = $page['title'];
		}

		$page['name'] = slug($page['name']);

		$page_id = $this->insert('page', $page);

		$dir = DIR_THEMES . $page['theme'] . '/template/page/' . $page['name'];

		_is_writable($dir);

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
			$this->url->setAlias($page['alias'], 'page/' . $page['name']);
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

		if (empty($page['theme'])) {
			$page['theme'] = option('config_default_theme', 'fluid');
		}

		if (empty($page['name'])) {
			$page['name'] = $this->getPageName($page_id);
		}

		$page['name'] = slug($page['name']);

		//Remove Old Directory if we are renaming page.
		$old_page = $this->queryRow("SELECT name, theme FROM " . $this->prefix . "page WHERE page_id = " . (int)$page_id);

		$dir = DIR_THEMES . $page['theme'] . '/template/page/' . $page['name'];

		if (isset($page['content'])) {
			if (_is_writable($dir)) {
				if ($page['content']) {
					if (file_put_contents($dir . '/content.tpl', html_entity_decode($page['content'])) === false) {
						$this->error['content'] = _l("There was an error wile writing the content for the page");
						return false;
					}
				}
			}
		}

		if (isset($page['style'])) {
			if (_is_writable($dir)) {
				if (file_put_contents($dir . '/style.less', $page['style']) === false) {
					$this->error['content'] = _l("There was an error wile writing the stylesheet for the page");
					return false;
				}
			}
		}

		if ($old_page && ($old_page['name'] !== $page['name'] || $page['theme'] !== $old_page['theme'])) {
			$old_dir = DIR_THEMES . $old_page['theme'] . '/template/page/' . $old_page['name'];
			if (is_dir($old_dir)) {
				rrmdir($old_dir);
			}
		}

		$this->update('page', $page, $page_id);

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
			$this->url->setAlias($page['alias'], 'page/' . $page['name']);
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
		$page['content'] = $this->getPageContent($page);
		$page['style']   = $this->getPageStyle($page);

		$this->addPage($page);
	}

	public function deletePage($page_id)
	{
		//Remove Old Directory if we are renaming page.
		$page = $this->queryRow("SELECT * FROM " . $this->prefix . "page WHERE page_id = " . (int)$page_id);

		if ($page) {
			$dir = DIR_THEMES . $page['theme'] . '/template/page/' . $page['name'];
			if (is_dir($dir)) {
				rrmdir($dir);
			}
		}

		$this->delete('page', $page_id);
		$this->delete('page_store', array('page_id' => $page_id));

		$this->url->removeAlias('page/' . $page['name']);

		$this->translation->deleteTranslation('page', $page_id);

		$this->cache->delete('page');

		return $page_id;
	}

	public function getPage($page_id)
	{
		$page = $this->queryRow("SELECT * FROM " . DB_PREFIX . "page WHERE page_id = " . (int)$page_id);

		if ($page) {
			$page['content'] = $this->getPageContent($page);
			$page['style']   = $this->getPageStyle($page);

			$page['alias'] = $this->url->getAlias('page/page', 'page_id=' . (int)$page_id);

			//Translations
			$translate_fields = array(
				'title',
				'meta_keywords',
				'meta_description',
			);

			$page['translations'] = $this->translation->getTranslations('page', $page_id, $translate_fields);
		}

		return $page;
	}

	//TODO: Develop good caching method for pages.
	public function getActivePage($page_id)
	{
		$store_id = option('store_id');

		$query =
			"SELECT * FROM " . DB_PREFIX . "page p" .
			" LEFT JOIN " . DB_PREFIX . "page_store ps ON (ps.page_id = p.page_id)" .
			" WHERE p.page_id = " . (int)$page_id . " AND p.status = 1 AND (ps.store_id = " . (int)$store_id . " OR ps.store_id IS NULL)";

		$page = $this->queryRow($query);

		if ($page) {
			$page['content'] = $this->getPageContent($page);
			$page['style']   = $this->getPageStyle($page);
		}

		return $page;
	}

	public function getPageName($page_id)
	{
		return $this->queryVar("SELECT name FROM " . $this->prefix . "page WHERE page_id = " . (int)$page_id);
	}

	public function getPageByName($name)
	{
		$store_id = option('store_id');
		$themes    = $this->theme->getStoreThemes();

		$query =
			"SELECT p.*, ps.layout_id, ps.store_id FROM " . DB_PREFIX . "page p" .
			" LEFT JOIN " . DB_PREFIX . "page_store ps ON (ps.page_id = p.page_id)" .
			" WHERE p.name = '" . $this->escape($name) . "' AND theme IN ('" . implode("','", $this->escape($themes)) . "')" .
			" AND p.status = 1 AND (ps.store_id = " . (int)$store_id . " OR ps.store_id IS NULL)";

		$page = $this->queryRow($query);

		$file = $this->theme->findFile('page/' . $name . '/content');

		//Page Does Not Exist, but found in database
		if ($page && !$file) {
			$this->deletePage($page['page_id']);
			$page = array();
		}

		if ($page) {
			$page['content'] = $this->getPageContent($page);
			$page['style']   = $this->getPageStyle($page);
		} elseif ($file) {
			if (!$this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "page WHERE `name` = '" . $this->escape($name) . "'")) {
				$page = array(
					'theme'         => option('config_theme'),
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
		$query = "SELECT p.*, ps.layout_id, ps.store_id FROM " . DB_PREFIX . "page p" .
			" LEFT JOIN " . $this->prefix . "page_store ps ON (ps.page_id = p.page_id) WHERE p.page_id = " . (int)$page_id;

		$page = $this->queryRow($query);

		if ($page) {
			$page += array(
				'layout_id' => option('config_default_layout_id')
			);

			$page['content'] = $this->getPageContent($page);
			$page['style']   = $this->getPageStyle($page);

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
			'title'  => 'like',
			'status' => 'equals',
		);

		$where = $this->extractFilter('page', $filter, $columns);

		if (!empty($filter['stores'])) {
			$store_ids = is_array($filter['stores']) ? $filter['stores'] : array($filter['stores']);

			if (!in_array(0, $store_ids)) {
				$from .= " LEFT JOIN " . DB_PREFIX . "page_store ps ON (p.page_id=ps.page_id)";
				$where .= " AND ps.store_id IN (" . implode(',', $store_ids) . ")";
			}
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
			$page['content'] = $this->getPageContent($page);
			$page['style']   = $this->getPageStyle($page);
		}
		unset($row);

		return $pages;
	}

	public function getPageContent($page)
	{
		$content = $this->theme->getFile('page/' . $page['name'] . '/content', $page['theme']);

		if (!$content) {
			$page_id = $this->queryVar("SELECT page_id FROM " . DB_PREFIX . "page WHERE `name` = '" . $this->escape($page['name']) . "' AND `theme` = '" . $this->escape($page['theme']) . "'");

			if ($page_id) {
				$this->delete('page', $page_id);
			}
		}

		return $content;
	}

	public function getPageStyle($page)
	{
		$style = $this->theme->getFile('page/' . $page['name'] . '/style.less', $page['theme']);

		if (!$style) {
			$style = $this->theme->getFile('page/' . $page['name'] . '/style.css', $page['theme']);
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
		$pages = cache('page.loaded');

		if (is_null($pages)) {
			$pages = array();

			$page_list = $this->getPages(array(), '*');

			foreach ($page_list as $p) {
				$pages[$p['theme']][$p['name']] = $p;
			}

			cache('page.loaded', $pages);
		}

		clearstatcache();

		$handle = opendir(DIR_THEMES);

		while (($theme = readdir($handle)) !== false) {
			if ($theme === '.' || $theme === '..' || $theme === 'admin') {
				continue;
			}

			if (filetype(DIR_THEMES . $theme) === 'dir') {
				$page_dir = DIR_THEMES . $theme . '/template/page/';

				if ($th = @opendir($page_dir)) {
					while (($name = readdir($th)) !== false) {
						if ($name === '.' || $name === '..') {
							continue;
						}

						if (filetype($page_dir . $name) === 'dir') {
							if (!isset($pages[$theme][$name])) {
								$title = array_map(function ($a) { return ucfirst($a); }, explode('_', $name));
								$title = implode(' ', $title);

								$content_file = $page_dir . $name . '/content.tpl';
								$style_file   = $page_dir . $name . '/style.less';
								$content      = is_file($content_file) ? file_get_contents($content_file) : '';
								$style        = is_file($style_file) ? file_get_contents($style_file) : '';

								$page = array(
									'theme'         => $theme,
									'name'          => $name,
									'title'         => $title,
									'template'      => '',
									'content'       => $content,
									'style'         => $style,
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
