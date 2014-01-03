<?php
class System_Extension_Shipping extends System_Extension_Model
{
	public function __construct($registry)
	{
		parent::__construct($registry);

		require_once(DIR_SYSTEM . "extension/shipping_extension.php");
	}

	public function has($code)
	{
		return $this->extensionExists('shipping', $code);
	}

	public function get($code)
	{
		return $this->loadClass('shipping', $code);
	}

	public function getActive()
	{
		$active = $this->cache->get('extension.shipping.active');

		if (is_null($active)) {
			$filter = array(
				'status' => 1,
			);

			$active = $this->getFiltered($filter);

			$this->cache->set('extension.shipping.active', $active);
		}

		$extensions = array();

		foreach ($active as $code => $extension) {
			$extensions[$code] = $this->loadClass('shipping', $code, $extension);
		}

		return $extensions;
	}

	public function updateExtension($code, $data)
	{
		$this->_updateExtension('shipping', $code, $data);

		$this->cache->delete('extension.shipping');
	}

	public function getFiltered($filter = array())
	{
		$extensions = $this->getExtensions('shipping', $filter);

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
		if (!$this->user->can('modify', 'extension/shipping')) {
			$this->error['permission'] = _l("User does not have permission to modify the Shipping Extension");
			return false;
		}

		$this->installExtension('shipping', $code);

		$this->cache->delete('extension.shipping');

		return true;
	}

	public function uninstall($code)
	{
		$this->uninstallExtension('shipping', $code);

		$this->cache->delete('extension.shipping');

		return true;
	}
}
