<?php
class Weight extends Library
{
	private $weights = array();

	//TODO: Update weight classes to stop using description table
	public function __construct()
	{
		parent::__construct();

		$weight_classes = $this->queryRows("SELECT * FROM " . DB_PREFIX . "weight_class wc LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wc.weight_class_id = wcd.weight_class_id) WHERE wcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($weight_classes as $weight_class) {
			$this->weights[$weight_class['weight_class_id']] = $weight_class;
		}

		$this->weight_class_id = $this->config->get('config_weight_class_id');
	}

	public function get($value, $weight_class_id)
	{
		if ($weight_class_id !== $this->weight_class_id) {
			$value = $this->convert($value, $weight_class_id, $this->weight_class_id);
		}

		return $value;
	}

	public function convert($value, $from, $to)
	{
		if ($from == $to) {
			return $value;
		}

		if (!isset($this->weights[$from]) || !isset($this->weights[$to])) {
			return $value;
		} else {
			$from = $this->weights[$from]['value'];
			$to   = $this->weights[$to]['value'];

			return $value * ($to / $from);
		}
	}

	public function format($value, $weight_class_id = null, $decimal_point = null, $thousand_point = null)
	{
		if (!$weight_class_id) {
			$weight_class_id = $this->config->get('config_weight_class_id');
		}

		if (!$decimal_point) {
			$decimal_point = $this->language->info('decimal_point');
		}

		if (!$thousand_point) {
			$thousand_point = $this->language->info('thousand_point');
		}

		if (isset($this->weights[$weight_class_id])) {
			$unit = $this->weights[$weight_class_id]['unit'];
		} else {
			$unit = '';
		}

		return number_format($value, 2, $decimal_point, $thousand_point) . $unit;
	}

	public function getUnit($weight_class_id)
	{
		if (isset($this->weights[$weight_class_id]['unit'])) {
			return $this->weights[$weight_class_id]['unit'];
		} else {
			return '';
		}
	}
}
