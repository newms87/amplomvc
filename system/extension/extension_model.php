<?php
abstract class ExtensionModel extends Model
{
	private $extensions;

	protected function _updateExtension($type, $code, $data)
	{
		$where = array(
			'type' => $type,
			'code' => $code,
		);

		$this->update('extension', $data, $where);
	}

	protected function getExtension($type, $code)
	{
		if ($this->extensionExists($type, $code)) {
			return $this->extensions[$type][$code];
		}

		return null;
	}

	protected function getExtensions($type)
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
						'settings'   => '',
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

				//Load Extension Language
				$_l = $this->language->system_fetch("extension/$type/$code");

				$extension['name'] = $_l['head_title'];
			}
			unset ($extension);

			$this->extensions[$type] = $extensions;
		}

		return $this->extensions[$type];
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

		$class = $this->loadClass($type, $code);

		if (method_exists($class, 'install')) {
			$class->install();
		}

		$data = array(
			'type'       => $type,
			'code'       => $code,
			'settings'   => '',
			'sort_order' => 0,
			'status'     => 1,
		);

		$this->insert('extension', $data);
	}

	protected function uninstallExtension($type, $code, $full = true)
	{
		if ($full) {
			$class = $this->loadClass($type, $code);

			if (method_exists($class, 'uninstall')) {
				$class->uninstall();
			}
		}

		$where = array(
			'type' => $type,
			'code' => $code,
		);

		$this->delete('extension', $where);
	}

	protected function loadClass($type, $code)
	{
		require_once(_ac_mod_file(DIR_SYSTEM . 'extension/extension.php'));
		require_once(_ac_mod_file(DIR_SYSTEM . 'extension/' . $type . '/' . $code . '.php'));

		$class = 'System_Extension_' . $this->tool->formatClassname($type) . '_' . $this->tool->formatClassname($code);

		return new $class($this->registry);
	}
}