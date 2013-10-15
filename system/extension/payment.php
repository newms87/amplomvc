<?php
class System_Extension_Payment extends ExtensionModel
{
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
		//Language
		$this->language->system('extension/payment');

		if (!$this->user->hasPermission('modify', 'extension/payment')) {
			$this->message->add('warning', 'error_permission');
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
