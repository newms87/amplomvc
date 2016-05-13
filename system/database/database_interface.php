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

interface DatabaseInterface
{
	public function getError();

	public function query($sql);

	public function escape($value);

	public function escapeHtml($value);

	public function countAffected();

	public function getLastId();
}
