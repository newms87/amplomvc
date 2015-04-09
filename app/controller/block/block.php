<?php

class App_Controller_Block_Block extends Controller
{
	public function delete()
	{
		$path = _get('path', '');

		if ($path) {
			if (!$this->block->remove($path)) {
				message('error', $this->block->fetchError());
			} else {
				message('success', _l("The Block %s was removed successfully!", $path));
			}
		}

		if (!$this->is_ajax) {
			redirect('admin/block', $_GET);
		}

		output_message();
	}

	public function save()
	{
		$path = _get('path', '');

		if ($path) {
			if (!$this->block->edit($path, $_POST)) {
				message('error', $this->block->fetchError());
			} else {
				message('success', _l("The Block %s was saved successfully!", $path));
			}
		}

		if (!$this->is_ajax) {
			redirect('admin/block');
		}

		output_message();
	}

	//override this method to add custom settings
	public function settings(&$block)
	{
		$block['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		return $this->render('block/block/settings', $block, true);
	}

	//Override this method to add custom instances
	public function instances(&$instances)
	{
		$default_instance = array(
			'name'       => _l("default"),
			'title'      => _l("Default"),
			'show_title' => 1,
			'settings'   => array(),
		);

		//AC Templates
		$instances['__ac_template__']         = $default_instance;
		$instances['__ac_template__']['name'] = 'instance-__ac_template__';

		$count = 1;
		foreach ($instances as $row => &$instance) {
			$instance['template'] = $this->instance($row, $instance, $count++ === count($instances));
		}
		unset($instance);

		$instance_data = array(
			'instances' => $instances,
		);

		return $this->render('block/block/instances', $instance_data, true);
	}

	//Override this method to add custom instance settings
	public function instance($row, $instance)
	{
		$data = array(
			'row'      => $row,
			'instance' => $instance,
		);

		$data['data_yes_no'] = array(
			0 => _l("No"),
			1 => _l("Yes"),
		);

		return $this->render('block/block/instance', $data, true);
	}
}
