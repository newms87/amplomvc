<?php
class System_Extension_Total extends System_Extension_Model
{
	public function __construct($registry)
	{
		parent::__construct($registry);

		require_once(DIR_SYSTEM . "extension/total_extension.php");
	}

	public function has($code)
	{
		return $this->extensionExists('total', $code);
	}

	public function get($code)
	{
		return $this->loadClass('total', $code);
	}

	public function getActive()
	{
		$active = $this->cache->get('extension.total.active');

		if (is_null($active)) {
			$filter = array(
				'status' => 1,
			);

			$active = $this->getFiltered($filter);

			$this->cache->set('extension.total.active', $active);
		}

		$extensions = array();

		foreach ($active as $code => $extension) {
			$extensions[$code] = $this->loadClass('total', $code, $extension);
		}

		return $extensions;
	}

	public function updateExtension($code, $data)
	{
		$this->_updateExtension('total', $code, $data);

		$this->cache->delete('extension.total');
	}

	public function getFiltered($filter = array())
	{
		$extensions = $this->getExtensions('total', $filter);

		return $extensions;
	}

	public function getTotal($filter = array())
	{
		unset($filter['start']);
		unset($filter['limit']);

		return count($this->getFiltered($filter));
	}

	public function install($code)
	{
		//Language
		$this->language->system('extension/total');

		if (!$this->user->can('modify', 'extension/total')) {
			$this->message->add('warning', 'error_permission');
			return false;
		}

		$this->installExtension('total', $code);

		$this->cache->delete('extension.total');

		return true;
	}

	public function uninstall($code)
	{
		$this->uninstallExtension('total', $code);

		$this->cache->delete('extension.total');

		return true;
	}
}
