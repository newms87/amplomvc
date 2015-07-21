<?php

class App_Model_Page extends App_Model_Table
{
	protected $table = 'page', $primary_key = 'page_id';
	protected $pages;

	public function __construct()
	{
		parent::__construct();

		$this->loadPages();
	}

	public function save($page_id, $page)
	{
		if (isset($page['title'])) {
			if (!validate('text', $page['title'], 1, 64)) {
				$this->error['title'] = _l("Page Title must be between 1 and 64 characters!");
			}
		} elseif (!$page_id) {
			$this->error['title'] = _l("Page Title is required.");
		}

		if (isset($page['theme'])) {
			if (empty($page['theme'])) {
				$this->error['theme'] = _l("The Page Theme must not be empty");
			}
		} elseif (!$page_id) {
			$page['theme'] = option('config_default_theme', AMPLO_DEFAULT_THEME);
		}

		if (isset($page['template'])) {
			if (empty($page['template'])) {
				$this->error['template'] = _l("The page Template cannot be empty");
			}
		} elseif (!$page_id) {
			$page['template'] = option('config_default_page_template', 'default');
		}

		if ($this->error) {
			return false;
		}

		if (empty($page['name']) && (!$page_id || isset($page['name']))) {
			$page['name'] = $page['title'];
		}

		//Format page name
		if (isset($page['name'])) {
			$page['name'] = slug($page['name']);
		}

		$orig = $this->getRecord($page_id);

		if ($page_id) {
			$name_changed  = isset($page['name']) && $page['name'] !== $orig['name'];
			$theme_changed = isset($page['theme']) && $page['theme'] !== $orig['theme'];

			//Remove old directory if the page directory has changed
			if ($name_changed || $theme_changed) {
				rrmdir(DIR_THEMES . $orig['theme'] . '/template/page/' . $orig['name']);
			}

			//Save page history if there have been changes
			foreach ($orig as $key => $value) {
				if (isset($page[$key]) && $page[$key] !== $value) {
					$this->saveHistory($page_id);
					break;
				}
			}
		}

		if (!empty($page['options'])) {
			$page['options'] = serialize($page['options']);
		}

		//Set Updated Date and User
		$page['date_updated']    = $this->date->now();
		$page['updated_user_id'] = $this->user->getId();

		if ($page_id) {
			$page_id = $this->update('page', $page, $page_id);
		} else {
			if (empty($page['options'])) {
				$page['options'] = serialize(array(
					'show_title'       => 1,
					'show_breadcrumbs' => 1,
				));
			}

			$page_id = $this->insert('page', $page);
		}

		if ($page_id) {
			$name  = isset($page['name']) ? $page['name'] : $orig['name'];
			$theme = isset($page['theme']) ? $page['theme'] : $orig['theme'];

			$dir = DIR_THEMES . $theme . '/template/page/' . $name;

			if (_is_writable($dir)) {
				if (isset($page['content'])) {
					if (file_put_contents($dir . '/content.tpl', html_entity_decode($page['content'])) === false) {
						$this->error['content'] = _l("There was an error writing the content for the page.");
					}
				}

				if (isset($page['style'])) {
					if (file_put_contents($dir . '/style.less', $page['style']) === false) {
						$this->error['style'] = _l("There was an error writing the stylesheet for the page.");
					}
				}
			} else {
				$this->error['content'] = _l("The directory %s was not writable.");
			}

			if (!empty($page['alias'])) {
				$this->url->setAlias($page['alias'], 'page/' . $page['name']);
			}

			if (!empty($page['translations'])) {
				$this->translation->setTranslations('page', $page_id, $page['translations']);
			}
		}

		clear_cache('page');

		if ($page_id && $page_id == option('terms_agreement_page_id')) {
			save_option('terms_agreement_date', $this->date->now());
		}

		return $page_id;
	}

	public function copyPage($page_id)
	{
		$page = $this->getRecord($page_id);

		return $this->addPage($page);
	}

	public function deletePage($page_id)
	{
		//Remove Directory
		$page = $this->getRecord($page_id);

		if ($page) {
			$dir = DIR_THEMES . $page['theme'] . '/template/page/' . $page['name'];
			if (is_dir($dir)) {
				rrmdir($dir);
			}
		}

		$this->delete('page', $page_id);
		$this->delete('page_history', array('page_id' => $page_id));

		$this->url->removeAlias('page/' . $page['name']);

		$this->translation->deleteTranslation('page', $page_id);

		clear_cache('page');

		return $page_id;
	}

	public function getRecord($page_id, $select = '*')
	{
		$page = parent::getRecord($page_id, $select);

		if ($page) {
			$this->getPageFiles($page);

			$page['options'] = $page['options'] ? unserialize($page['options']) : array();

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
		$page = $this->queryRow("SELECT * FROM {$this->t['page']} WHERE page_id = " . (int)$page_id . " AND status = 1");

		if ($page) {
			$page['options'] = unserialize($page['options']);

			$this->getPageFiles($page);
		}
		
		return $page;
	}

	public function getPageByName($name)
	{
		$name = str_replace('-', '_', $name);

		$themes = array_keys($this->theme->getThemes());

		$page = $this->queryRow("SELECT * FROM {$this->t['page']} WHERE status = 1 AND name = '" . $this->escape($name) . "' AND theme IN ('" . implode("','", $this->escape($themes)) . "')");

		if ($page) {
			$page['options'] = unserialize($page['options']);

			$this->getPageFiles($page);
		}

		return $page;
	}

	public function getPageForPreview($page_id)
	{
		$page = $this->queryRow("SELECT * FROM {$this->t['page']} WHERE page_id = " . (int)$page_id);

		if ($page) {
			$this->getPageFiles($page);

			$page += array(
				'layout_id' => option('config_default_layout_id')
			);

			$this->translation->translate('page', $page_id, $page);
		}

		return $page;
	}

	public function getRecords($sort = array(), $filter = array(), $options = array(), $total = false)
	{
		$records = parent::getRecords($sort, $filter, $options, $total);

		$total ? $rows = &$records[0] : $rows = &$records;

		foreach ($rows as &$row) {
			$this->getPageFiles($row);
		}
		unset($row);

		return $records;
	}

	public function getPageFiles(&$page)
	{
		if (!empty($page['theme']) && !empty($page['name'])) {
			$page['content_file'] = DIR_THEMES . $page['theme'] . '/template/page/' . $page['name'] . '/content.tpl';
			$page['style_file']   = DIR_THEMES . $page['theme'] . '/template/page/' . $page['name'] . '/style.less';
		}
	}

	public function compileStyle($page_id, $style)
	{
		$css = cache('page.' . $page_id . '.style');

		if ($css === null) {
			$css = trim($this->document->compileLessContent($style));

			if (false && !$css) {
				send_mail(array(
					'to'      => 'dnewman@roofscope.com',
					'subject' => "LESS COMPILE FAILED FOR " . $page_id,
					'html'    => $css . '<BR><BR>' . get_caller(),
				));
			}

			cache('page.' . $page_id . '.style', $css);
		}

		return $css;
	}

	public function saveHistory($page_id)
	{
		$page = $this->getRecord($page_id);

		$page['date']    = $this->date->now();
		$page['user_id'] = $page['updated_user_id'];

		return $this->insert('page_history', $page);
	}

	public function getTemplates($theme = null)
	{
		$theme = $theme ? $theme : option('site_theme', AMPLO_DEFAULT_THEME);

		$themes = array($theme => $theme) + $this->theme->getThemeParents($theme);

		$templates = array();

		foreach ($themes as $t) {
			$files = get_files(DIR_THEMES . $t . '/template/page/template/', 'tpl', FILELIST_STRING);

			foreach ($files as $f) {
				$name             = pathinfo($f, PATHINFO_FILENAME);
				$templates[$name] = $name;
			}
		}

		return $templates;
	}

	public function loadPages()
	{
		$this->pages = cache('page.loaded');

		if ($this->pages === null) {
			$this->pages = array();

			$page_list = $this->getRecords(null, null, array('cache' => true));

			foreach ($page_list as $p) {
				$this->pages[$p['theme']][$p['name']] = $p;
			}

			cache('page.loaded', $this->pages);
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
						if ($name === '.' || $name === '..' || $name === 'template') {
							continue;
						}

						if (filetype($page_dir . $name) === 'dir') {
							$this->syncPage($theme, $name);
						}
					}
				}
			}
		}

		closedir($handle);
	}

	protected function syncPage($theme, $name)
	{
		$page = isset($this->pages[$theme][$name]) ? $this->pages[$theme][$name] : false;

		$content_file = DIR_THEMES . $theme . '/template/page/' . $name . '/content.tpl';
		$style_file   = DIR_THEMES . $theme . '/template/page/' . $name . '/style.less';

		if ($page) {
			$time_updated = (int)strtotime($page['date_updated']);

			if ((is_file($content_file) && filemtime($content_file) > $time_updated)
				|| (is_file($style_file) && filemtime($style_file) > $time_updated)
			) {
				$update = array(
					'content' => is_file($content_file) ? file_get_contents($content_file) : '',
					'style'   => is_file($style_file) ? file_get_contents($style_file) : '',
				);

				$this->save($page['page_id'], $update);
			}
		} elseif (is_file($content_file)) {
			$page_data = get_comment_directives($content_file);
			$title     = !empty($page_data['title']) ? $page_data['title'] : cast_title($name);
			$cache     = isset($page_data['cache']) ? (int)$page_data['cache'] : 1;

			$content = file_get_contents($content_file);
			$style   = is_file($style_file) ? file_get_contents($style_file) : '';

			$page = array(
				'theme'    => $theme,
				'name'     => $name,
				'title'    => $title,
				'template' => !empty($page_data['template']) ? $page_data['template'] : null,
				'content'  => $content,
				'style'    => $style,
				'status'   => 1,
				'options'  => array(),
				'cache'    => $cache,
			);

			$this->save(null, $page);
		}
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
					'sort'         => true,
				)
			);
		}

		return $this->getTableColumns('page', $merge, $filter);
	}

	public function getViewListingId()
	{
		$view_listing_id = $this->Model_ViewListing->getViewListingBySlug('page_list');

		if (!$view_listing_id) {
			$view_listing = array(
				'name' => _l("Pages"),
				'slug' => 'page_list',
				'path' => 'admin/page/listing',
			);

			$view_listing_id = $this->Model_ViewListing->save(null, $view_listing);
		}

		return $view_listing_id;
	}
}
