<?php
abstract class System_Extension_Model extends Model
{
	private $extensions;

	protected function _updateExtension($type, $code, $data)
	{
		$where = array(
			'type' => $type,
			'code' => $code,
		);

		$data['settings'] = !empty($data['settings']) ? serialize($data['settings']) : '';

		$this->update('extension', $data, $where);
	}

	protected function getExtension($type, $code)
	{
		if ($this->extensionExists($type, $code)) {
			return $this->extensions[$type][$code];
		}

		return null;
	}

	protected function getExtensions($type, $filter = array())
	{
		if (!isset($this->extensions[$type])) {
			$extensions = $this->queryRows("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->escape($type) . "' ORDER BY sort_order ASC", 'code');

			$files = glob(DIR_SYSTEM . "extension/$type/*.php");

			foreach ($files as $file) {
				$code = basename($file, '.php');

				if (!isset($extensions[$code])) {
					$extensions[$code] = array(
						'type'       => $type,
						'code'       => $code,
						'title'      => $this->codeTitle($code),
						'settings'   => array(),
						'sort_order' => '',
						'status'     => 0,
						'installed'  => 0,
					);
				} else {
					$extensions[$code]['installed'] = 1;
				}
			}

			foreach ($extensions as $code => &$extension) {
				//The file does not exist
				if (!isset($extension['installed'])) {
					$this->uninstallExtension($type, $code, false);
					unset($extensions[$code]);
					continue;
				}

				$extension['settings'] = !empty($extension['settings']) ? unserialize($extension['settings']) : array();
			}
			unset ($extension);

			$this->extensions[$type] = $extensions;
		}

		if ($filter) {
			return $this->filter($this->extensions[$type], $filter);
		}

		return $this->extensions[$type];
	}

	private function filter($extensions, $filter)
	{
		foreach ($extensions as $key => $extension) {
			if (!empty($filter['name'])) {
				if (!preg_match("/$filter[name]/i", $extension['name'])) {
					unset($extensions[$key]);
				}
			}

			if (!empty($filter['code'])) {
				if (!preg_match("/$filter[code]/i", $extension['code'])) {
					unset($extensions[$key]);
				}
			}

			if (!empty($filter['sort_order'])) {
				if ((int)$extension['sort_order'] < $filter['sort_order']['low'] || (int)$extension['sort_order'] > $filter['sort_order']['high']) {
					unset($extensions[$key]);
				}
			}

			if (isset($filter['status'])) {
				if ((bool)$filter['status'] !== (bool)$extension['status']) {
					unset($extensions[$key]);
				}
			}
		}

		if (!empty($filter['sort'])) {
			$sort  = $filter['sort'];
			$order = (!empty($filter['order']) && $filter['order'] === 'DESC') ? 'DESC' : 'ASC';

			$sort_empty_last = function ($a, $b) use ($sort, $order) {
				if ($a[$sort] === '') {
					$a[$sort] = PHP_INT_MAX;
				}
				if ($b[$sort] === '') {
					$b[$sort] = PHP_INT_MAX;
				}
				return $order === 'DESC' ? $a[$sort] < $b[$sort] : $a[$sort] > $b[$sort];
			};

			usort($extensions, $sort_empty_last);
		}

		if (!empty($filter['limit'])) {
			$start      = !empty($filter['start']) ? max(0, (int)$filter['start']) : 0;
			$extensions = array_slice($extensions, $start, (int)$filter['limit']);
		}

		return $extensions;
	}

	protected function extensionExists($type, $code)
	{
		$this->getExtensions($type);

		return isset($this->extensions[$type][$code]);
	}

	protected function installExtension($type, $code)
	{
		$this->Admin_Model_User_UserGroup->addPermission($this->user->info('user_group_id'), 'access', "$type/$code");
		$this->Admin_Model_User_UserGroup->addPermission($this->user->info('user_group_id'), 'modify', "$type/$code");

		$data = array(
			'type'       => $type,
			'code'       => $code,
			'title'      => $this->codeTitle($code),
			'settings'   => '',
			'sort_order' => 0,
			'status'     => 1,
		);

		$extension = $this->loadClass($type, $code, $data);

		if (method_exists($extension, 'install')) {
			$extension->install();
		}

		$this->insert('extension', $data);
	}

	protected function uninstallExtension($type, $code, $full = true)
	{
		if ($full) {
			$extension = $this->loadClass($type, $code);

			if (method_exists($extension, 'uninstall')) {
				$extension->uninstall();
			}
		}

		$where = array(
			'type' => $type,
			'code' => $code,
		);

		$this->delete('extension', $where);
	}

	protected function loadClass($type, $code, $info = null)
	{
		require_once(_ac_mod_file(DIR_SYSTEM . 'extension/extension.php'));

		$ext_file = DIR_SYSTEM . 'extension/' . $type . '/' . $code . '.php';

		if (!is_file($ext_file)) {
			$this->message->add('warning', _l("Unable to load %s because %s was not found.", $code, $ext_file));
			return null;
		}

		require_once(_ac_mod_file($ext_file));

		$class = 'System_Extension_' . $this->tool->formatClassname($type) . '_' . $this->tool->formatClassname($code);

		$extension = new $class($this->registry);

		if (!$info) {
			$info = $this->getExtension($type, $code);
		}

		$extension->setInfo($info);

		return $extension;
	}

	private function codeTitle($code)
	{
		$title_parts = explode('_', $code);
		array_walk($title_parts, function (&$t) { $t = ucfirst($t); });
		return implode(' ', $title_parts);
	}
}
