<?php

class App_Model_Page extends Model
{
	public function __construct()
	{
		parent::__construct();

		//$this->loadPages();
	}

	public function addPage($page)
	{
		//Defaults
		$page += array(
			'title' => '',
			'theme' => option('config_default_theme', 'amplo'),
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

		if (isset($page['content'])) {
			file_put_contents($dir . '/content.tpl', $page['content']);
		}

		if (isset($page['style'])) {
			file_put_contents($dir . '/style.less', $page['style']);
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
			$page['theme'] = option('config_default_theme', AMPLO_DEFAULT_THEME);
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
		$page                 = $this->getPage($page_id);
		$page['content_file'] = $this->getPageContentFile($page);
		$page['style_file']   = $this->getPageStyleFile($page);

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

		$this->url->removeAlias('page/' . $page['name']);

		$this->translation->deleteTranslation('page', $page_id);

		$this->cache->delete('page');

		return $page_id;
	}

	public function getPage($page_id)
	{
		$page = $this->queryRow("SELECT * FROM " . DB_PREFIX . "page WHERE page_id = " . (int)$page_id);

		if ($page) {
			$page['content_file'] = $this->getPageContentFile($page);
			$page['style_file']   = $this->getPageStyleFile($page);

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
		$page = $this->queryRow("SELECT * FROM " . DB_PREFIX . "page WHERE page_id = " . (int)$page_id . " AND status = 1");

		if ($page) {
			$page['content_file'] = $this->getPageContentFile($page);
			$page['style_file']   = $this->getPageStyleFile($page);
		}

		return $page;
	}

	public function getPageName($page_id)
	{
		return $this->queryVar("SELECT name FROM " . $this->prefix . "page WHERE page_id = " . (int)$page_id);
	}

	public function getPageByName($name)
	{
		$themes = $this->theme->getStoreThemes();

		$query = "SELECT * FROM " . DB_PREFIX . "page WHERE status = 1 AND name = '" . $this->escape($name) . "' AND theme IN ('" . implode("','", $this->escape($themes)) . "')";

		$page = $this->queryRow($query);

		$file = $this->theme->findFile('page/' . $name . '/content');

		//Page Does Not Exist, but found in database
		if ($page && !$file) {
			$this->deletePage($page['page_id']);
			$page = array();
		}

		if ($page) {
			$page['content_file'] = $this->getPageContentFile($page);
			$page['style_file']   = $this->getPageStyleFile($page);
		} elseif ($file) {
			if (!$this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "page WHERE `name` = '" . $this->escape($name) . "'")) {
				$page_data = $this->tool->getFileCommentDirectives(DIR_THEMES . $file);
				$title     = !empty($page_data['title']) ? $page_data['title'] : ucfirst($name);
				$cache     = isset($page_data['cache']) ? (int)$page_data['cache'] : 1;

				$page = array(
					'theme'         => option('config_theme'),
					'name'          => $name,
					'title'         => $title,
					'layout_id'     => option('config_layout_id'),
					'content_file'  => DIR_THEMES . $file,
					'style_file'    => $this->theme->getFile('page/' . $name . '/style.less'),
					'status'        => 1,
					'display_title' => $title ? 1 : 0,
					'cache'         => $cache,
				);

				$page_id = $this->addPage($page);

				$page['page_id'] = $page_id;
			}
		}

		return $page;
	}

	public function getPageForPreview($page_id)
	{
		$page = $this->queryRow("SELECT * FROM " . DB_PREFIX . "page WHERE page_id = " . (int)$page_id);

		if ($page) {
			$page += array(
				'layout_id' => option('config_default_layout_id')
			);

			$page['content_file'] = $this->getPageContentFile($page);
			$page['style_file']   = $this->getPageStyleFile($page);

			$this->translation->translate('page', $page_id, $page);
		}

		return $page;
	}

	public function getPages($sort = array(), $filter = array(), $select = '*', $total = false, $index = null)
	{
		$select = $this->extractSelect('page', $select);

		//From
		$from = DB_PREFIX . "page";

		//Where
		$where = $this->extractWhere('page', $filter);

		//Order / Limit
		$order = $this->extractOrder($sort);
		$limit = $this->extractLimit($sort);

		//The Query
		$calc_rows = ($total && $this->calcFoundRows('page', $sort, $filter)) ? "SQL_CALC_FOUND_ROWS" : '';

		$rows = $this->queryRows("SELECT $calc_rows $select FROM $from WHERE $where $order $limit", $index);

		foreach ($rows as &$row) {
			$row['content_file'] = $this->getPageContentFile($row);
			$row['style_file']   = $this->getPageStyleFile($row);
		}
		unset($row);

		if ($total) {
			$query      = $calc_rows ? "SELECT FOUND_ROWS()" : "SELECT COUNT(*) FROM $from WHERE $where";
			$total_rows = $this->queryVar($query);

			return array(
				$rows,
				$total_rows
			);
		}

		return $rows;
	}

	public function getPageContentFile($page)
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

	public function getPageStyleFile($page)
	{
		$style = $this->theme->getFile('page/' . $page['name'] . '/style.less', $page['theme']);

		if (!$style) {
			$style = $this->theme->getFile('page/' . $page['name'] . '/style.css', $page['theme']);
		}

		return $style;
	}

	public function getTotalPages($filter = array())
	{
		return $this->getPages(null, $filter, 'COUNT(*)', false);
	}

	public function loadPages()
	{
		$pages = cache('page.loaded');

		if (is_null($pages)) {
			$pages = array();

			$page_list = $this->getPages();

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
								$this->syncPageDB($theme, $name);
							}
						}
					}
				}
			}
		}

		closedir($handle);
	}

	private function syncPageDB($theme, $name)
	{
		$title = array_map(function ($a) {
			return ucfirst($a);
		}, explode('_', $name));
		$title = implode(' ', $title);

		$content_file = DIR_THEMES . $theme . '/' . $name . '/content.tpl';
		$style_file   = DIR_THEMES . $theme . '/' . $name . '/style.less';
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

		return $this->addPage($page);
	}

	public function getColumns($filter = array())
	{
		static $merge;

		if (!$merge) {
			$merge = array(
				'status' => array(
					'type'         => 'select',
					'display_name' => _l("Status"),
					'build_data'   => array(
						0 => _l("Disabled"),
						1 => _l("Enabled"),
					),
					'filter'       => true,
					'sortable'     => true,
				)
			);
		}

		return $this->getTableColumns('page', $merge, $filter);
	}

	public function getViewListingId()
	{
		$view_listing_id = $this->Model_View->getViewListingBySlug('page_list');

		if (!$view_listing_id) {
			$view_listing = array(
				'name' => _l("Pages"),
				'slug' => 'page_list',
				'path' => 'admin/page/listing',
			);

			$view_listing_id = $this->Model_View->saveViewListing(null, $view_listing);
		}

		return $view_listing_id;
	}
}
