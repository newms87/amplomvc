<?php
class Admin_Controller_Block_Widget_Listing extends Controller
{
	public function build($settings)
	{
		$defaults = array(
			'template'      => 'table/list_view',
			'ajax'          => 1,
			'rows'          => array(),
			'template_data' => array(),
			'filter_value'  => array(),
			'pagination'    => true,
		);

		$settings += $defaults;

		$template_defaults = array(
			'listing_path' => !empty($settings['listing_path']) ? $settings['listing_path'] : $this->route->getPath(),
			'row_id'       => !empty($settings['row_id']) ? $settings['row_id'] : '',
		);

		$settings['template_data'] += $template_defaults;

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($settings['columns']);
		$this->table->setRows($settings['rows']);
		$this->table->setTemplateData($settings['template_data']);
		$this->table->mapAttribute('filter_value', $settings['filter_value']);

		$settings['listing'] = $this->table->render();

		if ($settings['pagination']) {
			$settings['pagination_settings'] = array(
				'total' => $settings['total_listings'],
				'path'  => $settings['listing_path'],
			);
		}


		//Render
		$this->render('block/widget/listing', $settings);
	}
}
