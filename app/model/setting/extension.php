<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class App_Model_Setting_Extension extends Model
{
	public function getExtensions($type)
	{
		return $this->queryRows("SELECT * FROM {$this->t['extension']} WHERE `type` = '" . $this->escape($type) . "' AND status = '1'");
	}

	public function getInstalled($type)
	{
		return $this->queryColumn("SELECT code FROM {$this->t['extension']} WHERE `type` = '" . $this->escape($type) . "'");
	}

	public function install($type, $code)
	{
		$ext = array(
			'type' => $type,
			'code' => $code,
		);

		$this->insert('extension', $ext);
	}

	public function uninstall($type, $code)
	{
		$where = array(
			'type' => $type,
			'code' => $code,
		);

		$this->delete('extension', $where);
	}
}
