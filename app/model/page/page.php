<?php

class App_Model_Page_Page extends Model
{
	private $page_theme;

	public function __construct()
	{
		parent::__construct();
		$this->page_theme = option('config_theme');

		if ($this->page_theme === 'admin') {
			$this->page_theme = 'fluid';
		}
	}

	public function addPage($data)
	{
		if (!validate('text', $data['title'], 3, 64)) {
			$this->error['title'] = _l("Page Title must be between 3 and 64 characters!");
		}

		if ($this->error) {
			return false;
		}

		$page_id = $this->insert('page', $data);

		$dir = DIR_THEMES . option('config_default_theme', 'fluid') . '/template/page/' . $data['name'];

		file_put_contents($dir . '/content.tpl', $data['content']);
		file_put_contents($dir . '/style.less', $data['style']);

		if (!empty($data['stores'])) {
			foreach ($data['stores'] as $store) {
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

		if (!empty($data['alias'])) {
			$this->url->setAlias($data['alias'], 'page/page', 'page_id=' . (int)$page_id);
		}

		if (!empty($data['translations'])) {
			$this->translation->setTranslations('page', $page_id, $data['translations']);
		}

		$this->cache->delete('page');

		return $page_id;
	}

	public function editPage($page_id, $data)
	{
		if (isset($data['title']) && !validate('text', $data['title'], 3, 64)) {
			$this->error['title'] = _l("Page Title must be between 3 and 64 characters!");
		}

		if ($this->error) {
			return false;
		}

		$this->update('page', $data, $page_id);

		$dir = DIR_THEMES . option('config_default_theme', 'fluid') . '/template/page/' . $data['name'];

		if (isset($data['content'])) {
			file_put_contents($dir . '/content.tpl', $data['content']);
		}

		if (isset($data['style'])) {
			file_put_contents($dir . '/content.tpl', $data['style']);
		}

		if (isset($data['stores'])) {
			$this->delete('page_store', array('page_id' => $page_id));

			foreach ($data['stores'] as $store) {
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

		if (isset($data['alias'])) {
			$this->url->setAlias($data['alias'], 'page/page', 'page_id=' . (int)$page_id);
		}

		if (isset($data['translations'])) {
			$this->translation->setTranslations('page', $page_id, $data['translations']);
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

	public function getPages($data = array(), $select = null, $total = false)
	{
		//Select
		if ($total) {
			$select = 'COUNT(*) as total';
		} elseif (!$select) {
			$select = '*';
		}

		//From
		$from = DB_PREFIX . "page p";

		//Where
		$where = 'WHERE 1';

		if (isset($data['title'])) {
			$where .= " AND p.title like '%" . $this->escape($data['title']) . "%'";
		}

		if (!empty($data['stores'])) {
			$store_ids = is_array($data['stores']) ? $data['stores'] : array($data['stores']);

			$from .= " LEFT JOIN " . DB_PREFIX . "page_store ps ON (p.page_id=ps.page_id)";

			$where .= " AND ps.store_id IN (" . implode(',', $store_ids) . ")";
		}

		if (isset($data['status'])) {
			$where .= " AND p.status = '" . ($data['status'] ? 1 : 0) . "'";
		}

		//Order By & Limit
		if (!$total) {
			$order = $this->extractOrder($data);
			$limit = $this->extractLimit($data);
		} else {
			$order = '';
			$limit = '';
		}

		//The Query
		$query = "SELECT $select FROM $from $where $order $limit";

		//Execute
		$result = $this->query($query);

		//Process Results
		if ($total) {
			return $result->row['total'];
		}

		foreach ($result->rows as &$page) {
			$page['content'] = $this->getPageContent($page['name']);
			$page['style']   = $this->getPageStyle($page['name']);
		}
		unset($row);

		return $result->rows;
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
		return $this->getPages($data, null, true);
	}
}
