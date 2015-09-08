<?php

/**
 * Title: URL Aliases
 * Icon: alias.png
 * Order: 7
 */
class App_Controller_Admin_Settings_UrlAlias extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("URL Aliases"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings'));
		breadcrumb(_l("URL Aliases"), site_url('admin/settings/url-alias'));

		//Batch Actions
		$actions = array(
			'enable'  => array(
				'label' => _l("Enable")
			),
			'disable' => array(
				'label' => _l("Disable"),
			),
			'delete'  => array(
				'label' => _l("Delete"),
			),
		);

		$data['batch_action'] = array(
			'actions' => $actions,
			'url'     => site_url('admin/settings/url-alias/batch-action'),
		);

		$data['view_listing_id'] = $this->Model_UrlAlias->getViewListingId();

		//Render
		output($this->render('settings/url_alias/list', $data));
	}

	public function listing($listing = array())
	{
		$sort    = (array)_get('sort', array('alias' => 'ASC'));
		$filter  = (array)_get('filter');
		$options = array(
			'index'   => 'url_alias_id',
			'page'    => _get('page'),
			'limit'   => _get('limit', option('admin_list_limit', 20)),
			'columns' => $this->Model_UrlAlias->getColumns((array)_request('columns')),
		);

		list($url_aliases, $total) = $this->Model_UrlAlias->getRecords($sort, $filter, $options, true);

		foreach ($url_aliases as $url_alias_id => &$url_alias) {
			$url_alias['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/settings/url_alias/form', 'url_alias_id=' . $url_alias_id)
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/settings/url_alias/remove', 'url_alias_id=' . $url_alias_id)
				)
			);
		}
		unset($url_alias);

		$listing += array(
			'extra_cols'     => $this->Model_UrlAlias->getColumns(false),
			'records'        => $url_aliases,
			'sort'           => $sort,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total' => $total,
			'listing_path'   => 'admin/settings/url_alias/listing',
			'save_path'      => 'admin/settings/url_alias/save',
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
		set_page_info('title', _l("URL Aliases"));

		//Insert or Update
		$url_alias_id = isset($_GET['url_alias_id']) ? (int)$_GET['url_alias_id'] : 0;

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings'));
		breadcrumb(_l("URL Aliases"), site_url('admin/settings/url-alias'));

		if (!$url_alias_id) {
			breadcrumb(_l("Add"), site_url('admin/settings/url-alias/udpate'));
		} else {
			breadcrumb(_l("Edit"), site_url('admin/settings/url_alias/save', 'url_alias_id=' . $url_alias_id));
		}

		//Load Information
		$url_alias = $_POST;

		if ($url_alias_id && !IS_POST) {
			$url_alias = $this->Model_UrlAlias->getRecord($url_alias_id);
		}

		//Load Values or Defaults
		$defaults = array(
			'url_alias_id' => $url_alias_id,
			'alias'        => '',
			'path'         => '',
			'query'        => '',
			'redirect'     => '',
			'status'       => 1,
		);

		$url_alias += $defaults;

		$url_alias['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		//Render
		output($this->render('settings/url_alias/form', $url_alias));
	}

	public function save()
	{
		if ($url_alias_id = $this->Model_UrlAlias->save(_request('url_alias_id'), $_POST)) {
			message('success', _l("The alias has been saved!"));
			message('data', array('url_alias_id' => $url_alias_id));
		} else {
			message('error', $this->Model_UrlAlias->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} elseif ($this->message->has('error')) {
			post_redirect('admin/settings/url_alias');
		} else {
			redirect('admin/settings/url_alias');
		}
	}

	public function remove()
	{
		if ($this->Model_UrlAlias->remove(_get('url_alias_id'))) {
			message('success', _l("The Alias has been removed."));
		} else {
			message('error', $this->Model_UrlAlias->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/settings/url_alias');
		}
	}

	public function batch_update()
	{
		foreach (_post('batch', array()) as $url_alias_id) {
			switch (_post('action')) {
				case 'enable':
					$this->Model_UrlAlias->save($url_alias_id, array('status' => 1));
					break;

				case 'disable':
					$this->Model_UrlAlias->save($url_alias_id, array('status' => 0));
					break;

				case 'delete':
					$this->Model_UrlAlias->remove($url_alias_id);
					break;
			}
		}

		if ($this->Model_UrlAlias->hasError()) {
			message('error', $this->Model_UrlAlias->fetchError());
		} else {
			message('success', _l("Users were updated successfully!"));
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/settings/url_alias');
		}
	}
}
