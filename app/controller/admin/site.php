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

class App_Controller_Admin_Site extends Controller
{
	public function index($data = array())
	{
		//Page Head
		set_page_info('title', _l("Sites"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Sites"), site_url('admin/site'));

		//Render
		output($this->render('site/list', $data));
	}

	public function listing()
	{
		$sort    = (array)_request('sort', array('name' => 'ASC'));
		$filter  = (array)_request('filter');
		$options = array(
			'index'   => 'site_id',
			'page'    => _get('page', 1),
			'limit'   => _get('limit', option('admin_list_limit', 20)),
			'columns' => $this->Model_Site->getColumns((array)_request('columns')),
		);

		list($sites, $total) = $this->Model_Site->getRecords($sort, $filter, $options, true);

		foreach ($sites as $site_id => &$site) {
			$actions = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/site/form', 'site_id=' . $site_id)
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/site/remove', 'site_id=' . $site_id)
				),
			);

			$site['actions'] = $actions;
		}
		unset($site);

		$listing = array(
			'extra_cols'     => $this->Model_Site->getColumns(false),
			'records'        => $sites,
			'sort'           => $sort,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total' => $total,
			'listing_path'   => 'admin/site/listing',
			'save_path'      => 'admin/site/save',
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
		set_page_info('title', _l("Site Information"));

		//Insert or Update
		$site_id = _get('site_id', 0);

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Sites"), site_url('admin/site'));
		breadcrumb(_l("Site"), site_url('admin/site/form', 'site_id=' . $site_id));

		//Store Data
		$site = $_POST;

		if ($site_id && !IS_POST) {
			$site = $this->Model_Site->getRecord($site_id);
		}

		$defaults = array(
			'site_id' => $site_id,
			'name'    => 'Site ' . $site_id,
			'domain'  => '',
			'url'     => '',
			'ssl'     => '',
			'prefix'  => DB_PREFIX,
		);

		$site += $defaults;

		//Render
		output($this->render('site/form', $site));
	}

	public function save()
	{
		if ($this->Model_Site->save(_request('site_id'), $_POST)) {
			message('success', _l("The Site has been saved."));
		} else {
			message('error', $this->Model_Site->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} elseif ($this->message->has('error')) {
			post_redirect('admin/site/form', 'site_id=' . _get('site_id'));
		} else {
			redirect('admin/site');
		}
	}

	public function remove()
	{
		if ($this->Model_Site->remove(_request('site_id'))) {
			message('success', _l("The Site was removed!"));
		} else {
			message('error', $this->Model_Site->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/site');
		}
	}
}
