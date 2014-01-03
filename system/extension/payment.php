<?php
class System_Extension_Payment extends System_Extension_Model
{
	public function __construct($registry)
	{
		parent::__construct($registry);

		require_once(DIR_SYSTEM . "extension/payment_extension.php");
	}

	public function has($code)
	{
		return $this->extensionExists('payment', $code);
	}

	public function get($code)
	{
		return $this->loadClass('payment', $code);
	}

	public function getActive()
	{
		$active = $this->cache->get('extension.payment.active');

		if (is_null($active)) {
			$filter = array(
				'status' => 1,
			);

			$active = $this->getFiltered($filter);

			$this->cache->set('extension.payment.active', $active);
		}

		$extensions = array();

		foreach ($active as $code => $extension) {
			$extensions[$code] = $this->loadClass('payment', $code, $extension);
		}

		return $extensions;
	}

	public function updateExtension($code, $data)
	{
		$this->_updateExtension('payment', $code, $data);

		$this->cache->delete('extension.payment');
	}

	public function getFiltered($filter = array())
	{
		$extensions = $this->getExtensions('payment', $filter);

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
		if (!$this->user->can('modify', 'extension/payment')) {
			$this->error['permission'] = _l("You do not have permission to modify the Payment extensions.");
			return false;
		}

		$this->installExtension('payment', $code);

		$this->cache->delete('extension.payment');

		return true;
	}

	public function uninstall($code)
	{
		$this->uninstallExtension('payment', $code);

		$this->cache->delete('extension.payment');

		return true;
	}
}
