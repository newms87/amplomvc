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
		//The Table Columns
		$columns = $this->Model_UrlAlias->getColumns(_request('columns'));

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('alias', 'ASC');
		$filter = _get('filter', array());

		list($url_aliases, $url_alias_total) = $this->Model_UrlAlias->getRecords($sort, $filter, $columns, true, 'url_alias_id');

		foreach ($url_aliases as $url_alias_id => &$url_alias) {
			$url_alias['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => site_url('admin/settings/url_alias/save', 'url_alias_id=' . $url_alias_id)
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => site_url('admin/settings/url_alias/remove', 'url_alias_id=' . $url_alias_id)
				)
			);
		}
		unset($url_alias);

		$listing += array(
			'row_id'         => 'url_alias_id',
			'columns'        => $columns,
			'extra_cols'     => $this->Model_UrlAlias->getColumns(false),
			'rows'           => $url_aliases,
			'filter_value'   => $filter,
			'pagination'     => true,
			'total_listings' => $url_alias_total,
			'listing_path'   => 'admin/settings/url_alias/listing',
		);

		$output = block('widget/listing', null, $listing);

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
		if ($this->Model_UrlAlias->save(_request('url_alias_id'), $_POST)) {
			message('success', _l("The alias has been saved!"));
		} else {
			message('error', $this->Model_UrlAlias->getError());
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
			message('error', $this->Model_UrlAlias->getError());
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
			message('error', $this->Model_UrlAlias->getError());
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
