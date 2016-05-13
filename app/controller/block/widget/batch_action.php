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

/**
 * Class App_Controller_Block_Widget_BatchAction
 * Name: Batch Action Widget
 */
class App_Controller_Block_Widget_BatchAction extends App_Controller_Block_Block
{
	/**
	 * @param $settings = array(
	 *                  'actions' => array(),
	 *                  'url' => 'string', - URL to batch action method
	 *                  'selector' => 'string',
	 *                  );
	 *
	 * actions - The Batch Actions available to apply on the items
	 *
	 * path - The controller path (eg: 'catalog/product/batch_update') to post the batch action data to. This Controller
	 * method should handle applying the batch actions.
	 *
	 * replace (optional) - The Element to replace the contents of with the refreshed item listings
	 *
	 * selector (optional) - The Sizzle (same as jQuery) selector matching checkbox input elements whose values
	 *                       are the ID's of the items you wish to apply the batch action to.
	 *
	 */

	public function build($settings)
	{
		//No Actions == nothing to do
		if (empty($settings['actions']) || empty($settings['url'])) {
			return;
		}

		if (empty($settings['selector'])) {
			$settings['selector'] = '[name="batch[]"]';
		}

		foreach ($settings['actions'] as $key => &$action) {
			if (!isset($action['key'])) {
				$action['key'] = $key;
			}

			//TODO: Legacy Compatibility. Remove in future version
			if (!empty($action['type'])) {
				if (!isset($action['default'])) {
					$action['default'] = '';
				}

				$action['build'] += array(
					'type'   => $action['type'],
					'data'   => array(),
					'select' => $action['default'],
				);
			}

			if (!empty($action['build'])) {
				$action['build'] += array(
					'type'   => 'text',
					'name'   => 'action_value',
					'select' => '',
				);
			}
		}
		unset($action);

		$this->render('block/widget/batch_action', $settings);
	}
}
