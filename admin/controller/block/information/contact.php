<?php
/**
 * Name: Contact
 */
class Admin_Controller_Block_Information_Contact extends Controller
{
	public function settings(&$settings)
	{
		$defaults = array(
			'contact_info' => _l("Please feel free to contact us with any questions!"),
		);

		foreach ($defaults as $key => $default) {
			if (!isset($settings[$key])) {
				$settings[$key] = $default;
			}
		}

		//Send data to template
		$data['settings'] = $settings;

		//Render
		$this->render('block/information/contact_settings', $data);
	}

	public function save()
	{
		return $this->error;
	}
}
