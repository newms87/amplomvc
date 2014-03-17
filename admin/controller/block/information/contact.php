<?php
/**
 * Name: Contact
 */
class Admin_Controller_Block_Information_Contact extends Controller
{
	public function settings(&$settings)
	{
		//Template-(Lanuage preloaded)
		$this->view->load('block/information/contact_settings');

		$defaults = array(
			'contact_info' => _l("Please feel free to contact us with any questions!"),
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

	public function save()
	{
		return $this->error;
	}
}
