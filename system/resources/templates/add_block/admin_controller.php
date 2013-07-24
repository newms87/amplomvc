<?php
class __class_name__ extends Controller
{
	__settings_start__
	public function settings(&$settings)
	{
		//Template-(Lanuage preloaded)
		$this->template->load('block/__route___settings');
		
		//This is an example (feel free to remove this if you dont need it!)
		$defaults = array(
			'my_variable' => $this->_('text_my_variable_default'),
		);
		
		foreach ($defaults as $key => $default) {
			if (!isset($settings[$key])) {
				$settings[$key] = $default;
			}
		}
		
		$this->data['settings'] = $settings;
		
		$this->render();
	}
	__settings_end__
	
	__profile_start__
	public function profile(&$profiles)
	{
		$this->template->load('block/__route___profile');
		
		//Add your code here
		
		$this->data['profiles'] = $profiles;
		
		$this->render();
	}
	__profile_end__
	
	public function validate()
	{
		return $this->error;
	}
}