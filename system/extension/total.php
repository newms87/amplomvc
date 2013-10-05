<?php
class System_Extension_Total extends ExtensionModel
{
	public function has($code)
	{
		return $this->extensionExists('total', $code);
	}

	public function get($code)
	{
		return $this->getExtension('total', $code);
	}

	public function getAll()
	{
		return $this->getExtensions('total');
	}

	public function getActive()
	{
		$active = $this->cache->get('extension.total.active');

		if (is_null($active)) {
			$filter = array(
				'status' => 1,
			);

			$active = $this->getFilteredExtensions($filter);

			$this->cache->set('extension.total.active', $active);
		}

		$extensions = array();

		foreach ($active as $code => $extension) {
			$extensions[$code] = $this->loadClass('total', $code);
			$extensions[$code]->setInfo($extension);
		}

		return $extensions;
	}

	public function updateExtension($code, $data)
	{
		$this->_updateExtension('total', $code, $data);

		$this->cache->delete('extension_total');
	}

	public function getFilteredExtensions($filter = array())
	{
		$extensions = $this->getExtensions('total');

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

			if (!empty($filter['status'])) {
				if ((bool)$filter['status'] !== (bool)$extension['status']) {
					unset($extensions[$key]);
				}
			}
		}

		if (!empty($filter['sort'])) {
			$sort = $filter['sort'];
			$order = ( !empty($filter['order']) && $filter['order'] === 'DESC' ) ? 'DESC' : 'ASC';

			usort($extensions, function($a,$b) use($sort, $order) { return $order === 'DESC' ? $a[$sort] < $b[$sort] : $a[$sort] > $b[$sort]; });
		}

		if (!empty($filter['limit'])) {
			$start = !empty($filter['start']) ? max(0,(int)$filter['start']) : 0;
			$extensions = array_slice($extensions, $start, (int)$filter['limit']);
		}

		return $extensions;
	}

	public function getTotalExtensions($filter = array())
	{
		unset($filter['start']);
		unset($filter['limit']);

		return count($this->getFilteredExtensions($filter));
	}

	public function install($code)
	{
		//Language
		$this->language->system('extension_total');

		if (!$this->user->hasPermission('modify', 'extension/total')) {
			$this->message->add('warning', 'error_permission');
			return false;
		}

		$this->installExtension('total', $code);

		$this->cache->delete('extension_total');

		return true;
	}

	public function uninstall($code)
	{
		$this->uninstallExtension('total', $code);

		$this->cache->delete('extension_total');

		return true;
	}
}