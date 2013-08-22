<?php
class Rss
{
	private $xml;
	private $format;
	private $items = array();

	public function load($url, $format = 'rss')
	{
		$this->format = $format;

		$this->xml = simplexml_load_file($url);

		foreach ($this->xml->channel->item as $item) {
			$this->items[] = $item->children();
		}
	}

	public function next_item()
	{
		return next($this->items);
	}

	public function get($key)
	{
		$item = current($this->items);

		$value = $item->$key;

		return html_entity_decode($value, ENT_QUOTES);
	}

	public function count()
	{
		return count($this->items);
	}
}