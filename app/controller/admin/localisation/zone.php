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

class App_Controller_Admin_Localisation_Zone extends App_Controller_Table
{
	protected $model = array(
		'title' => 'Zone',
		'class' => 'App_Model_Localisation_Zone',
		'path'  => 'admin/localisation_zone',
		'label' => 'name',
		'value' => 'zone_id',
	);

	public function index($options = array())
	{
		$options += array(
			'batch_action' => array(
				'actions' => array(
					'enable'  => array(
						'label' => _l("Enable"),
					),
					'disable' => array(
						'label' => _l("Disable"),
					),
					'delete'  => array(
						'label' => _l("Delete"),
					),
				),
			),
		);

		return parent::index($options);
	}

	public function listing($options = array())
	{
		$options += array(
			'sort_default' => array('name' => 'ASC'),
		);

		return parent::listing($options);
	}

	public function form($options = array())
	{
		$options = array(
			'defaults' => array(
				'zone_id'    => 0,
				'status'     => 1,
				'name'       => '',
				'code'       => 0,
				'country_id' => 0,
			),
		);

		return parent::form($options);
	}

	public function batch_action($options = array())
	{
		$options += array(
			'callback' => function ($batch, $action, $value) {
				foreach ($batch as $category_id) {
					switch ($action) {
						case 'enable':
							$this->instance->save($category_id, array('status' => 1));
							break;

						case 'disable':
							$this->instance->save($category_id, array('status' => 0));
							break;

						case 'delete':
							$this->instance->remove($category_id);
							break;
					}
				}
			},
		);

		return parent::batch_action($options);
	}
}
