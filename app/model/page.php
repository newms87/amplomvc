<?php

/**
 * @author  Daniel Newman
 * @date    3/20/2013
 * @package Amplo MVC
 * @link    http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */
class App_Model_Page extends App_Model_Table
{
	protected $table = 'page', $primary_key = 'page_id';

	const
		STATUS_DISABLED = 0,
		STATUS_PENDING = 1,
		STATUS_PUBLISHED = 2;

	static $statuses = array(
		self::STATUS_DISABLED  => 'Disabled',
		self::STATUS_PENDING   => 'Pending',
		self::STATUS_PUBLISHED => 'Published',
	);

	public function save($page_id, $page)
	{
		if (isset($page['title'])) {
			if (!validate('text', $page['title'], 1, 256)) {
				$this->error['title'] = _l("Page Title must be between 1 and 256 characters!");
			}
		} elseif (!$page_id) {
			$this->error['title'] = _l("Page Title is required.");
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

		//Format page name and ensure it is unique.
		if (isset($page['name'])) {
			$page['name'] = slug($page['name']);

			$count = 1;
			$name  = $page['name'];

			while ($this->queryVar("SELECT COUNT(*) FROM {$this->t['page']} WHERE `name` = '" . $this->escape($name) . "' AND page_id != " . (int)$page_id)) {
				$name = $page['name'] . '-' . $count++;
			}

			$page['name'] = $name;
		}

		if (!empty($page['options']) && !is_string($page['options'])) {
			$page['options'] = json_encode($page['options']);
		}

		//Set Updated Date and User
		$page['date_updated']    = $this->date->now();
		$page['updated_user_id'] = $this->user->getId();

		//$orig / $updated for Page verification
		$orig    = $this->getRecord($page_id);
		$updated = $page + $orig;


		//Before Save Business Logic
		if ($page_id) {
			//Remove old directory if the page directory has changed
			if ($updated['name'] !== $orig['name'] || $updated['type'] !== $orig['type']) {
				rrmdir(DIR_SITE . 'app/view/template/' . $orig['type'] . '/' . $orig['name']);
			}

			if (isset($page['status']) && $page['status'] != $orig['status']) {
				if ($orig['status'] == self::STATUS_PUBLISHED) {
					$page['date_published'] = '';
				} elseif ($page['status'] == self::STATUS_PUBLISHED) {
					if (!$updated['date_published'] || $updated['date_published'] === DATETIME_ZERO) {
						$page['date_published'] = $this->date->now();
					}
				}
			}

			//Save page history if there have been changes
			foreach ($orig as $key => $value) {
				if (isset($page[$key]) && $page[$key] !== $value) {
					$this->saveHistory($page_id);
					break;
				}
			}
		} else {
			if (empty($page['status'])) {
				$page['status'] = self::STATUS_PENDING;
			}

			if ($page['status'] === self::STATUS_PUBLISHED) {
				$page['date_published'] = $this->date->now();
			}

			$page['date_created'] = $this->date->now();

			if (empty($page['type'])) {
				$page['type'] = 'page';
			}
		}

		$page_id = parent::save($page_id, $page);

		//After Save Business Logic
		if ($page_id) {
			$dir = DIR_SITE . 'app/view/template/' . $updated['type'] . '/' . $updated['name'] . '/';

			$updated['dir']          = $dir;
			$updated['content_file'] = $dir . 'content.tpl';
			$updated['style_file']   = $dir . 'style.less';

			$this->syncPage($updated);

			if (!empty($page['alias'])) {
				$this->url->setAlias($page['alias'], $page['type'] . '/' . $page['name']);
			}

			if (!empty($page['translations'])) {
				$this->translation->setTranslations('page', $page_id, $page['translations']);
			}

			if (isset($page['categories'])) {
				$this->delete('page_category', array('page_id' => $page_id));

				foreach ($page['categories'] as $category_id) {
					$page_category = array(
						'page_id'     => $page_id,
						'category_id' => $category_id,
					);

					$this->insert('page_category', $page_category);
				}
			}
		}

		//If this page is the terms agreement page, reset the modified date to notify users.
		if ($page_id && $page_id == option('terms_agreement_page_id')) {
			save_option('terms_agreement_date', $this->date->now());
		}

		return $page_id;
	}

	public function remove($page_id)
	{
		//Remove Directory
		$page = $this->getRecord($page_id);

		if ($page) {
			$dir = DIR_SITE . 'app/view/template/' . $page['type'] . '/' . $page['name'];

			if (is_dir($dir)) {
				rrmdir($dir);
			}
		}

		$this->delete('page_history', array('page_id' => $page_id));
		$this->url->removeAlias('page/' . $page['name']);
		$this->translation->deleteTranslation('page', $page_id);

		return parent::remove($page_id);
	}

	public function getPage($name, $published = true)
	{
		$page = is_numeric($name) ? $this->getRecord($name) : $this->findRecord(array('name' => $name));

		if (!$page) {
			if ($file = theme_dir('template/page/' . $name . '/content.tpl')) {
				$new_page = array(
					'type'         => 'page',
					'name'         => $name,
					'content_file' => $file,
					'style_file'   => theme_dir('template/page/' . $name . '/style.less'),
				);

				if ($page_id = $this->syncPageFromFile($new_page)) {
					$page = $this->getRecord($page_id);
				}
			}
		}

		if ($page) {
			$this->pageDetails($page);

			if ($published && $page['status'] !== App_Model_Page::STATUS_PUBLISHED) {
				return false;
			}
		}

		return $page;
	}

	public function getPageForPreview($page_id)
	{
		$page = $this->queryRow("SELECT * FROM {$this->t['page']} WHERE page_id = " . (int)$page_id);

		if ($page) {
			$this->pageDetails($page);

			$this->translation->translate('page', $page_id, $page);
		}

		return $page;
	}

	public function getRecords($sort = array(), $filter = array(), $options = array(), $total = false)
	{
		if (isset($filter['category_id'])) {
			$options['join']['page_category'] = array(
				'type'  => "LEFT JOIN",
				'alias' => 'pc',
				'on'    => 'page_id',
			);

			$filter['#category_id'] = " AND " . $this->extractWhere('page_category pc', $filter);
		}

		return parent::getRecords($sort, $filter, $options, $total);
	}

	public function getPages($sort, $filter = array(), $options = array(), $total = false)
	{
		$filter['status'] = self::STATUS_PUBLISHED;

		if (isset($filter['keywords'])) {
			$keywords            = $this->escape($filter['keywords']);
			$filter['#keywords'] = " AND (content LIKE '%$keywords%' OR excerpt LIKE '%$keywords%' OR title LIKE '%$keywords%')";
		}

		$records = $this->getRecords($sort, $filter, $options, $total);

		$total ? $rows = &$records[0] : $rows = &$records;

		foreach ($rows as &$row) {
			$this->pageDetails($row);
		}
		unset($row);

		return $records;
	}

	public function getCategories($page_id)
	{
		return $this->queryColumn("SELECT category_id FROM {$this->t['page_category']} WHERE page_id = " . (int)$page_id);
	}

	public function pageDetails(&$page)
	{
		if (!empty($page['type']) && !empty($page['name'])) {
			$dir = DIR_SITE . 'app/view/template/' . $page['type'] . '/' . $page['name'] . '/';

			$page['dir']          = $dir;
			$page['content_file'] = $dir . 'content.tpl';
			$page['style_file']   = $dir . 'style.less';

			$this->syncPage($page);

			if (!empty($page['status']) && $page['status'] !== App_Model_Page::STATUS_PUBLISHED) {
				if ($this->date->isInPast($page['date_published'], false)) {
					$page['status'] = App_Model_Page::STATUS_PUBLISHED;
					$this->save($page['page_id'], array('status' => App_Model_Page::STATUS_PUBLISHED));
				}
			}
		}

		if (!empty($page['options']) && is_string($page['options'])) {
			$page['options'] = (array)json_decode($page['options']);
		}

		$page['meta'] = $this->Model_Meta->get('page', $page['page_id']);
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

		$files = get_files(DIR_SITE . 'app/view/template/page_template/', 'tpl', FILELIST_STRING);

		foreach ($themes as $t) {
			$files += get_files(DIR_THEMES . $t . '/template/page_template/', 'tpl', FILELIST_STRING);
		}

		$templates = array();

		foreach ($files as $f) {
			$name             = pathinfo($f, PATHINFO_FILENAME);
			$templates[$name] = $name;
		}

		return $templates;
	}

	public function getAuthors()
	{
		$authors = cache('user_role.authors');

		if (!$authors) {
			$user_role_ids = array();

			$roles = $this->Model_UserRole->getRecords();

			foreach ($roles as $role) {
				if ($this->Model_UserRole->can($role['user_role_id'], 'w', 'admin/page/form')) {
					$user_role_ids[] = $role['user_role_id'];
				}
			}

			$authors = $this->Model_User->getRecords(array('username' => 'ASC'), array('user_role_id' => $user_role_ids), array('index' => 'user_id'));

			cache('user_role.authors', $authors);
		}

		return $authors;
	}

	public function getColumns($filter = array(), $merge = array())
	{
		$merge += array(
			'status'          => array(
				'type'   => 'select',
				'label'  => _l("Status"),
				'build'  => array(
					'data' => App_Model_Page::$statuses,
				),
				'filter' => true,
				'sort'   => true,
			),
			'author_id'       => array(
				'type'   => 'select',
				'label'  => _l("Author"),
				'build'  => array(
					'data'  => $this->getAuthors(),
					'label' => 'username',
					'value' => 'user_id',
				),
				'filter' => 'multiselect',
				'sort'   => true,
			),
			'updated_user_id' => array(
				'type'     => 'select',
				'label'    => _l("Updated By"),
				'build'    => array(
					'data'  => $this->getAuthors(),
					'label' => 'username',
					'value' => 'user_id',
				),
				'filter'   => 'multiselect',
				'sort'     => true,
				'editable' => false,
			),
			'template'        => array(
				'type'   => 'select',
				'label'  => 'Template',
				'build'  => array(
					'data' => $this->getTemplates(),
				),
				'filter' => 'multiselect',
				'sort'   => true,
			),
			'date_created'    => array(
				'editable' => false,
			),
			'date_updated'    => array(
				'editable' => false,
			),
		);

		return parent::getColumns($filter, $merge);
	}

	protected function syncPage($page)
	{
		if (!empty($page['page_id'])) {
			$update       = array();
			$time_updated = (int)strtotime($page['date_updated']);

			if (!_is_writable($page['dir'])) {
				trigger_error(_l("Unable to write to page directory %s", $page['dir']));
			}

			//Sync Content File
			if (is_file($page['content_file'])) {
				if (filemtime($page['content_file']) > $time_updated) {
					$update['content'] = file_get_contents($page['content_file']);
				}
			} elseif (@file_put_contents($page['content_file'], html_entity_decode($page['content'])) === false) {
				$this->error['content'] = _l("There was an error writing the content for the page.");
			} else {
				$this->plugin->gitIgnore($page['content_file']);
			}

			//Sync Style File
			if (is_file($page['style_file'])) {
				if (filemtime($page['style_file']) > $time_updated) {
					$update['style'] = file_get_contents($page['style_file']);
				}
			} elseif (@file_put_contents($page['style_file'], $page['style']) === false) {
				$this->error['style'] = _l("There was an error writing the stylesheet for the page.");
			} else {
				$this->plugin->gitIgnore($page['style_file']);
			}

			//Update if necessary
			if ($update) {
				$this->save($page['page_id'], $update);
			}
		}
	}

	protected function syncPageFromFile($page)
	{
		if (is_file($page['content_file'])) {
			$page_data = get_comment_directives($page['content_file']);
			$title     = !empty($page_data['title']) ? $page_data['title'] : cast_title($page['name']);
			$cache     = isset($page_data['cache']) ? (int)$page_data['cache'] : 1;

			$content = file_get_contents($page['content_file']);
			$style   = is_file($page['style_file']) ? file_get_contents($page['style_file']) : '';

			$page = array(
				'type'     => $page['type'],
				'name'     => $page['name'],
				'title'    => $title,
				'template' => !empty($page_data['template']) ? $page_data['template'] : null,
				'content'  => $content,
				'style'    => $style,
				'status'   => 1,
				'options'  => array(),
				'cache'    => $cache,
			);

			return $this->save(null, $page);
		}
	}

	public function cron()
	{
		//TODO: Build page cron.. should publish pages and maybe other stuff??? sync pages should be handled in real time, so dont do that in cron.
	}
}
