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

class System_Mod_Mod
{
	protected $error = array();

	public function hasError()
	{
		return !empty($this->error);
	}

	public function getError()
	{
		return $this->error;
	}

	public function fetchError()
	{
		$error       = $this->error;
		$this->error = array();

		return $error;
	}

	public function clearError()
	{
		$this->error = array();
	}

	public function apply($source, $mod, $file_type, $meta)
	{
		return file_get_contents($mod);
	}
}
