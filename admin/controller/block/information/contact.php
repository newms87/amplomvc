<?php
class Admin_Controller_Block_Information_Contact extends Controller
{
	public function settings(&$settings)
	{
		//Template-(Lanuage preloaded)
		$this->template->load('block/information/contact_settings');
		
		$defaults = array(
			'contact_info' => $this->_('text_contact_info_default'),
		);
		
		foreach ($defaults as $key => $default) {
			if (!isset($settings[$key])) {
				$settings[$key] = $default;
			}
		}
		
		//Send data to template
		$this->data['settings'] = $settings;
		
		//Render
		$this->render();
	}
	
	public function validate()
	{
		return $this->error;
	}
}
