<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class Router
{
	protected
		$action,
		$path,
		$segments,
		$nodes,
		$args = array(),
		$site,
		$routing_hooks = array();

	public function __get($key)
	{
		global $registry;

		return $registry->get($key);
	}

	public function isPath($path)
	{
		return preg_match("#^" . str_replace('-', '_', $path) . "$#", $this->path);
	}

	public function getPath()
	{
		return $this->path;
	}

	public function setPath($path, $nodes = null, $segments = null)
	{
		$path = path_format($path, false);

		$base = trim(SITE_BASE, '/');

		if ($base && strpos($path, $base) === 0) {
			$path = trim(substr($path, strlen($base)), '/');
		}

		if ($path) {
			$url_alias = $this->url->alias2Path($path);

			if ($url_alias) {
				$path = $url_alias['path'];

				if ($url_alias['query']) {
					$_GET = $url_alias['query'] + $_GET;
				}
			}
		} else {
			$path = option('homepage_path', 'index');
		}

		$this->path = str_replace('-', '_', $path);

		$this->segments = $segments === null ? explode('/', $path) : (array)$segments;
		$this->nodes    = $nodes === null ? explode('/', $this->path) : (array)$segments;

		foreach ($this->segments as &$seg) {
			$seg = str_replace('-', '_', $seg);
		}
	}

	public function getSegment($index = null)
	{
		if ($index === null) {
			return $this->segments;
		}

		return isset($this->segments[$index]) ? $this->segments[$index] : '';
	}

	public function getNode($index = null)
	{
		if ($index === null) {
			return $this->nodes;
		}

		return isset($this->nodes[$index]) ? $this->nodes[$index] : '';
	}

	public function getAction()
	{
		return $this->action;
	}

	public function getArgs()
	{
		return $this->args;
	}

	public function setArgs($args)
	{
		$this->args = (array)$args;
	}

	public function getSite()
	{
		return $this->site;
	}

	public function setSite(array $site)
	{
		global $_options;

		if (!is_array($_options)) {
			$_options = array();
		}

		$site += array(
			'site_id' => 0,
			'name'    => 'Amplo MVC',
			'domain'  => DOMAIN,
			'url'     => URL_SITE,
			'ssl'     => HTTPS_SITE,
			'prefix'  => DB_PREFIX,
		);

		_set_prefix($site['prefix']);

		$settings = cache('setting.config');

		if (!$settings) {
			//TODO: Should use $this->loadGroup('config');
			$settings = $this->db->queryRows("SELECT * FROM {$this->db->t['setting']} WHERE auto_load = 1", 'key');

			foreach ($settings as &$setting) {
				$setting = $setting['serialized'] ? unserialize($setting['value']) : $setting['value'];
			}
			unset($setting);

			cache('setting.config', $settings);
		}

		$_options = $site + $settings + $_options;

		$this->url->setUrl($site['url']);
		$this->url->setSsl($site['ssl']);

		$this->site = $site;
	}

	public function routeRequest()
	{
		$options = array(
			'cache' => true,
			'index' => 'site_id',
		);

		$sites = $this->Model_Site->getRecords(null, null, $options);

		$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
		$url    = $scheme . str_replace('www', '', $_SERVER['HTTP_HOST']) . '/' . trim($_SERVER['REQUEST_URI'], '/');

		$routed_site = array();

		foreach ($sites as $site) {
			if (strpos($url, trim($site['url'], '/ ')) === 0 || strpos($url, trim($site['ssl'], '/ ')) === 0) {
				$routed_site = $site;
				break;
			}
		}

		$this->setSite($routed_site);
		$this->setPath(preg_replace("/\\?.*$/", '', $_SERVER['REQUEST_URI']));

		//Resolve routing hooks
		uasort($this->routing_hooks, function ($a, $b) {
			return $a['sort_order'] > $b['sort_order'];
		});

		foreach ($this->routing_hooks as $hook) {
			if ($hook['callable']($this) === false) {
				break;
			}
		}

		//Resolve Layout ID
		set_option('config_layout_id', $this->getLayoutForPath($this->path));

		//Verify Amplo Version & Settings
		if (IS_ADMIN && $this->path !== 'admin/settings/restore_defaults') {
			$amplo_version = option('AMPLO_VERSION');

			if (!$amplo_version) {
				//redirect('admin/settings/restore-defaults');
			} elseif (AMPLO_AUTO_UPDATE && $amplo_version !== AMPLO_VERSION) {
				if ($this->System_Update->updateSystem(AMPLO_VERSION)) {
					message('notify', _l("The database version %s was out of date and has been updated to version %s", $amplo_version, AMPLO_VERSION));
				} else {
					message('error', _l("Failed to update to Amplo %s. Please contact the web admin as this may cause system instalbility.", AMPLO_VERSION));
				}
			}
		}

		if (!IS_AJAX) {
			$query = http_build_query($_GET);
			$this->request->addHistory($this->path . ($query ? '?' . $query : ''));
		}
	}

	public function registerHook($name, $callable, $sort_order = 0)
	{
		if (is_callable($callable)) {
			$this->routing_hooks[$name] = array(
				'callable'   => $callable,
				'sort_order' => $sort_order,
			);

			return true;
		}

		return false;
	}

	public function unregisterHook($name)
	{
		unset($this->routing_hooks[$name]);
	}

	public function getLayoutForPath($path)
	{
		$layouts = cache('layout.routes');

		if ($layouts === null) {
			$layouts = $this->Model_Layout->getLayoutRoutes();

			cache('layout.routes', $layouts);
		}

		foreach ($layouts as $layout) {
			if (strpos($path, $layout['route']) === 0) {
				return $layout['layout_id'];
			}
		}

		return option('config_default_layout_id');
	}

	public function dispatch()
	{
		if (AMPLO_ACCESS_LOG) {
			$this->logRequest();
		}

		//Dispatch Route
		$this->action = new Action($this->path, $this->args);

		$valid = $this->action->isValid();

		if ($valid) {
			if (IS_ADMIN) {
				if (!$this->user->canDoAction($this->action)) {
					if (!is_logged()) {
						$invalid_paths = array(
							'admin/user/login',
							'admin/user/logout',
						);

						if (in_array($this->path, $invalid_paths)) {
							$this->request->setRedirect('admin');
						} else {
							$this->request->setRedirect($this->url->here());
						}

						if (request_accepts('application/json')) {
							echo json_encode(array('error' => _l("You are not logged in. You are being redirected to the log in page.<script>window.location = '%s'</script>", site_url('admin/user/login'))));
							exit;
						}

						redirect('admin/user/login');
					}

					$this->action = new Action('admin/error/permission');
				}
			}
		}

		if (!$valid || !$this->action->execute()) {
			if (strpos($this->path, 'api/') === 0) {
				output_api('error', _l("The API resource %s was not found.", $this->path), null, 404);
			} else {
				$this->action = new Action(option('error_404_path', 'error/not_found'));
				$this->action->execute();
			}
		}

		output_flush();
	}

	protected function logRequest()
	{
		global $_access_log;

		if (!empty($_access_log['only'])) {
			$match = false;

			foreach ($_access_log['only'] as $only) {
				if (preg_match("#$only#", $this->path)) {
					$match = true;
					break;
				}
			}

			if (!$match) {
				return;
			}
		}

		if (!empty($_access_log['skip'])) {
			foreach ($_access_log['skip'] as $skip) {
				if (preg_match("#$skip#", $this->path)) {
					return;
				}
			}
		}

		if (IS_POST) {
			$post = $_POST;

			$private = array(
				'password',
			);

			if (!empty($_access_log['private'])) {
				$private = array_merge($private, $_access_log['private']);
			}

			foreach ($private as $p) {
				if (isset($post[$p])) {
					$post[$p] = '...';
				}
			}
		}

		write_log('access-log', (IS_ADMIN ? 'ADMIN ' : '') . (IS_POST ? "POST " : "GET ") . (IS_AJAX ? 'AJAX ' : '') . $this->path . (IS_POST ? "<BR><BR>" . json_encode($post) : ''));
	}
}
