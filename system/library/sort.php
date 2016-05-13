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

//TODO: Move this to Block widget/limit

class Sort extends Library
{
	//TODO: Move this to the admin panel
	static $limits = array(
		5   => '5',
		10  => '10',
		20  => '20',
		50  => '50',
		100 => '100',
		0   => 'all'
	);

	public function renderLimits($settings = array())
	{
		$settings += array(
			'template'   => 'block/widget/limit',
			'limits'     => self::$limits,
			'path'       => $this->router->getPath(),
			'limit_text' => '(see more)',
			'limit'      => _get('limit', 0),
			'total'      => null,
		);

		$limit = (int)$settings['limit'];

		$template_file = $this->theme->getFile('template/' . $settings['template']);

		if (!$template_file) {
			trigger_error(_l("%s(): Limit template %s was found!", __METHOD__, $template_file));

			return;
		}

		//Set limit for pagination compatibility
		$_GET['limit'] = $limit;

		$settings['limit_url'] = site_url($settings['path'], _get_exclude('limit', 'page') + array('limit' => ''));
		$settings['limit']     = $limit;

		$settings['show_more'] = (!$settings['total'] || $limit < $settings['total']) ? $settings['limit_url'] . ($limit + option('limit_more_count', 10)) : false;

		return render_file($template_file, $settings);
	}
}
