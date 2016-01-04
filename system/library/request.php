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

class Request
{
	public function __construct()
	{
		if (empty($_SESSION['page_requests'])) {
			$_SESSION['page_requests'] = array();
		}
	}

	public function addHistory($url)
	{
		$_SESSION['page_requests'][] = $url;
	}

	public function hasRedirect($context = '')
	{
		$key = $context ? 'redirect_' . $context : 'redirect';
		return !empty($_SESSION[$key]);
	}

	public function doRedirect($context = '')
	{
		if ($this->hasRedirect($context)) {
			redirect($this->fetchRedirect($context));
		}
	}

	public function fetchRedirect($context = '')
	{
		$redirect = $this->getRedirect($context);

		$this->clearRedirect($context);

		return $redirect;
	}

	public function setRedirect($url = '', $query = '', $context = '')
	{
		$key = $context ? 'redirect_' . $context : 'redirect';
		$_SESSION[$key] = site_url($url, $query);
	}

	public function clearRedirect($context = '')
	{
		$key = $context ? 'redirect_' . $context : 'redirect';
		unset($_SESSION[$key]);
	}

	public function getRedirect($context = '')
	{
		$key = $context ? 'redirect_' . $context : 'redirect';
		return !empty($_SESSION[$key]) ? $_SESSION[$key] : null;
	}

	/**
	 * Redirect the browser by sending a javascript redirect call.
	 * Warning: This will only work if the users browser has JS enabled! Make sure this is the case (ie: call this from an AJAX request)
	 *
	 * @param $url - The full url or the controller path. If the full URL (eg: starting with http(s):// ) is given, Url::redirect() will ignore $query.
	 * @param mixed $query - a string URI or associative array to be converted into a string URI
	 */
	public function redirectBrowser($url)
	{
		echo "<script type=\"text/javascript\">location=\"$url\"</script>";
		exit;
	}

	public function getPrevPageRequest($offset = -2)
	{
		return current(array_slice($_SESSION['page_requests'], $offset, 1));
	}
}
