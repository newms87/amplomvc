<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

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
