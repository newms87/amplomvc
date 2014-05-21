<?php

class Catalog_Model_Page_Page extends Model
{
	//TODO: Develop good caching method for pages.
	public function getActivePage($page_id)
	{
		$store_id = option('store_id');

		$query =
			"SELECT * FROM " . DB_PREFIX . "page p" .
			" LEFT JOIN " . DB_PREFIX . "page_store ps ON(ps.page_id=p.page_id)" .
			" WHERE p.page_id='" . (int)$page_id . "' AND ps.store_id IN ('-1', '$store_id')";

		$page = $this->queryRow($query);

		if (!$page) {
			if ($this->queryVar("SELECT COUNT(*) FROM " . DB_PREFIX . "page WHERE page_id = " . (int)$page_id)) {
				//Page is not for this store
				return false;
			}
			//Page Does not exist
			return null;
		}

		//Page is inactive
		if (!$page['status']) {
			return false;
		}

		$page['content'] = $this->theme->findFile('page/' . $page['name'] . '/content');

		if (!$page['content']) {
			trigger_error(_l("The page %s content file was not found. Add page/%s/content.tpl to your theme", $page['name'], $page['name']));
		}

		$page['style'] = $this->theme->findFile('page/' . $page['name'] . '/style.less');

		if (!$page['style']) {
			$page['style'] = $this->theme->findFile('page/' . $page['name'] . '/style.css');
		}

		$this->translation->translate('page', $page_id, $page);

		return $page;
	}

	public function getPageByName($name)
	{
		$content = $this->theme->findFile('page/' . $name . '/content');

		if (!$content) {
			return array();
		}

		//Data
		$page = $this->queryRow("SELECT * FROM " . DB_PREFIX . "page WHERE `name` = '" . $this->escape($name) . "'");

		if (!$page) {
			$page = array(
				'layout_id'     => option('config_layout_id'),
				'title'         => ucfirst($name),
				'display_title' => 1,
				'name'          => $name,
				'status'        => 1,
				'cache'         => 1,
			);

			$page['page_id'] = $this->insert('page', $page);
		}

		//Content
		$page['content'] = $content;

		//Style
		$page['style'] = $this->theme->findFile('page/' . $page['name'] . '/style.less');

		if (!$page['style']) {
			$page['style'] = $this->theme->findFile('page/' . $page['name'] . '/style.css');
		}

		return $page;
	}

	public function getPageForPreview($page_id)
	{
		$page = $this->queryRow("SELECT * FROM " . DB_PREFIX . "page WHERE page_id = " . (int)$page_id);

		if ($page) {
			$page_content = $this->theme->findFile('page/' . $page['name'] . '/content');

			if ($page_content) {
				$page['content'] = file_get_contents($page_content);
			} else {
				$page['content'] = '';
				trigger_error(_l("The page %s content file was not found. Add page/%s/content.tpl to your theme", $page['name'], $page['name']));
			}

			$page_style = $this->theme->findFile('page/' . $page['name'] . '/style.less');

			echo $page_style . '<BR>';
			if ($page_style) {
				$page_style = $this->document->compileLess($page_style, 'page.' . $page_id);
			} else {
				$page_style = $this->theme->findFile('page/' . $page['name'] . '/style.css');
			}

			$page['style'] = $page_style ? file_get_contents($page_style) : '';

			$this->translation->translate('page', $page_id, $page);
		}

		return $page;
	}
}
