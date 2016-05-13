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

class App_Controller_Admin_ApiUser extends App_Controller_Table
{
	protected $model = array(
		'title' => 'API User',
		'class' => 'App_Model_ApiUser',
		'path'  => 'admin/api_user',
		'label' => 'username',
		'value' => 'api_user_id',
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
			'sort_default' => array('username' => 'ASC'),
		);

		return parent::listing($options);
	}

	public function form($options = array())
	{
		$options += array(
			'defaults' => array(
				'api_user_id'  => 0,
				'user_id'      => user_info('user_id'),
				'username'     => user_info('username'),
				'user_role_id' => option('default_api_user_role_id'),
				'api_key'      => '',
				'public_key'   => '',
				'private_key'  => '',
				'status'       => 1,
			),
			'template' => 'api_user/form',
			'data'     => array(
				'users'      => $this->Model_User->getRecords(null, null, array('cache' => true)),
				'user_roles' => $this->Model_UserRole->getRecords(null, array('type' => App_Model_UserRole::TYPE_API), array('cache' => true)),
			),
		);

		return parent::form($options);
	}

	public function batch_action($options = array())
	{
		$options += array(
			'callback' => function ($batch, $action, $value) {
				foreach ($batch as $api_user_id) {
					switch ($action) {
						case 'enable':
							$this->instance->save($api_user_id, array('status' => 1));
							break;

						case 'disable':
							$this->instance->save($api_user_id, array('status' => 0));
							break;

						case 'delete':
							$this->instance->remove($api_user_id);
							break;
					}
				}
			},
		);

		return parent::batch_action($options);
	}
}
