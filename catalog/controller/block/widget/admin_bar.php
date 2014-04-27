<?php

class Catalog_Controller_Block_Widget_AdminBar extends Controller
{
	public function build($settings = array())
	{
		if (!empty($_COOKIE['disable_admin_bar'])) {
			return;
		}

		$data = $settings;

		$data += array(
			'admin_bar' => $this->config->get('config_admin_bar'),
		   'admin_link' => $this->url->admin(),
		   'clock_time' => $this->date->now('datetime_long'),
		);

		$time_inc            = 3600 * 24;
		$data['sim_forward'] = $this->url->here('sim_time=' . $time_inc);
		$data['sim_back']    = $this->url->here('sim_time=-' . $time_inc);
		$data['sim_reset']   = $this->url->here('sim_time=reset');

		//Render
		$this->render('block/widget/admin_bar', $data);
	}
}