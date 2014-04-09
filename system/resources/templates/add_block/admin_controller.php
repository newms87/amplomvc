<?php

class __class_name__ extends Controller
{
__settings_start__
	public function settings(&$settings)
	{
		//This is an example (feel free to remove this if you dont need it!)
		$defaults = array(
			'my_variable' => _l("My Variable"),
		);

		foreach ($defaults as $key => $default) {
			if (!isset($settings[$key])) {
				$settings[$key] = $default;
			}
		}

		$data = array(
			'settings' => $settings,
		);

		$this->render('block/__path___settings', $data);
	}
__settings_end__

__profile_start__
	public function profile(&$profiles)
	{
		$data = array(
			'profiles' => $profiles,
		);

		//Add your code here

		$this->render('block/__path___profile', $data);
	}
__profile_end__
}
