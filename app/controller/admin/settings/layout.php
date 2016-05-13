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

class App_Controller_Admin_Settings_Layout extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("Layouts"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings'));
		breadcrumb(_l("Layouts"), site_url('admin/settings/layout'));

		//Listing
		$data['listing'] = $this->listing();

		//Batch Actions
		$actions = array(
			'enable'  => array(
				'label' => _l("Enable")
			),
			'disable' => array(
				'label' => _l("Disable"),
			),
			'copy'    => array(
				'label' => _l("Copy"),
			),
			'delete'  => array(
				'label' => _l("Delete"),
			),
		);

		$data['batch_action'] = array(
			'actions' => $actions,
			'url'     => site_url('admin/logs/batch-action'),
		);

		//Render
		output($this->render('settings/layout/list', $data));
	}

	public function listing()
	{
		$sort    = (array)_get('sort', array('name' => 'ASC'));
		$filter  = (array)_get('filter');
		$options = array(
			'index'   => 'layout_id',
			'page'    => _get('page'),
			'limit'   => _get('limit', option('admin_list_limit', 20)),
			'columns' => $this->Model_Layout->getColumns((array)_request('columns')),
		);

		list($layouts, $total) = $this->Model_Layout->getRecords($sort, $filter);

		foreach ($layouts as $layout_id => &$layout) {
			$layout['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/settings/layout/form', 'layout_id=' . $layout_id)
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/settings/layout/delete', 'layout_id=' . $layout_id)
				)
			);

			$routes           = $this->Model_Layout->getRoutes($layout_id);
			$layout['routes'] = implode('<br />', array_column($routes, 'route'));
		}
		unset($layout);

		$listing = array(
			'extra_cols'     => $this->Model_Log->getColumns(false),
			'records'        => $layouts,
			'sort'           => $sort,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total' => $total,
			'listing_path'   => 'admin/layout/listing',
			'save_path'      => 'admin/layout/save',
		);

		$output = block('widget/listing', null, $listing + $options);

		//Response
		if ($this->is_ajax) {
			output($output);
		}

		return $output;
	}

	public function form()
	{
		//Page Head
		set_page_info('title', _l("Layouts"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Layouts"), site_url('admin/settings/layout'));

		//Insert or Update
		$layout_id = _get('layout_id');

		$layout = $_POST;

		//Load Information
		if ($layout_id && !IS_POST) {
			$layout           = $this->Model_Layout->getRecord($layout_id);
			$layout['routes'] = $this->Model_Layout->getRoutes($layout_id);
		}

		//Set Values or Defaults
		$defaults = array(
			'name'   => '',
			'routes' => array(),
		);

		$layout += $defaults;

		//Template Defaults
		$layout['routes']['__ac_template__'] = array(
			'route' => '',
		);

		//Template Data
		$layout['data_stores'] = $this->Model_Site->getRecords(null, null, array('cache' => true));

		//Action Buttons
		$layout['save']   = site_url('admin/settings/layout/save', 'layout_id=' . $layout_id);
		$layout['cancel'] = site_url('admin/settings/layout');

		//Render
		output($this->render('layout/form', $layout));
	}

	public function save()
	{
		if ($layout_id = $this->Model_Layout->save(_get('layout_id'), $_POST)) {
			message('success', _l("Success: You have modified layouts!"));
			message('data', array('layout_id' => $layout_id));
		} else {
			message('error', $this->Model_Layout->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/settings/layout');
		}
	}

	public function delete()
	{
		if ($this->Model_Layout->remove(_get('layout_id'))) {
			message('success', _l("The layout has been removed."));
		} else {
			message('error', $this->Model_Layout->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/settings/layout');
		}
	}

	public function batch_action()
	{
		$batch  = (array)_request('batch');
		$action = _request('action');
		$value  = _request('value');

		foreach ($batch as $layout_id) {
			switch ($action) {
				case 'enable':
					$this->Model_Layout->save($layout_id, array('status' => 1));
					break;
				case 'disable':
					$this->Model_Layout->save($layout_id, array('status' => 0));
					break;
				case 'delete':
					$this->Model_Layout->remove($layout_id);
					break;
				case 'copy':
					$this->Model_Layout->copy($layout_id);
					break;

				default:
					break 2; // Break For Loop
			}
		}

		if ($this->Model_Layout->hasError()) {
			message('error', $this->Model_Layout->fetchError());
		} else {
			message('success', _l("Success: You have modified layouts!"));
		}

		if ($this->is_ajax) {
			$this->listing();
		} else {
			redirect('admin/settings/layout');
		}
	}
}
