<?php
abstract class Extension extends Controller
{
	protected $info;
	protected $settings;
	protected $language;

	public function __construct($registry)
	{
		parent::__construct($registry);

		//Create an independent copy of language for extensions avoid overwriting mistakes!)
		$lang = $registry->get('language');
		$this->language = new Language($registry, $lang->id(), false, false);
		$this->language->data = $lang->data;
	}

	public function isActive()
	{
		return $this->info['status'];
	}

	public function getCode()
	{
		return $this->info['code'];
	}

	public function info()
	{
		return $this->info;
	}

	public function setInfo($info)
	{
		$this->info = $info;

		if (isset($info['settings'])) {
			$this->settings = $info['settings'];
		}
	}

	public function getInfo($key = null)
	{
		if ($key) {
			return isset($this->info[$key]) ? $this->info[$key] : null;
		}

		return $this->info;
	}

	public function getSettings()
	{
		return $this->settings;
	}

	public function renderTemplate()
	{
		$this->template->load('payment/payment');

		$this->data['confirm'] = $this->url->link('payment/confirm', 'code=' . $this->info['code'] . '&order_id=' . $this->order->getId());

		$this->render();
	}

	protected function confirmUrl()
	{
		return $this->url->link('payment/confirm', 'code=' . $this->info['code'] . '&order_id=' . $this->order->getId());
	}

	protected function callbackUrl($callback = 'callback', $query = '')
	{
		return $this->url->link('payment/callback', 'code=' . $this->info['code'] . '&callback=' . $callback . ($query?'&'.$query:''));
	}

	public function validate($address, $total)
	{
		if ((int)$this->settings['min_total'] > $total) {
			return false;
		}

		if (!$this->address->inGeoZone($address, $this->settings['geo_zone_id'])) {
			return false;
		}

		return true;
	}
}
