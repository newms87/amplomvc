<?php
class App_Controller_Admin_Extension_Total_Shipping extends Controller
{
	/**
	 * This method extends the Settings for a Total extension.
	 * It is expected to render a template with HTML form elements with names in the format as follows.
	 * eg: <input type="text" name="settings[extension_var]" value="<?= $settings['extension_var']; ?>" />
	 *
	 * @param $settings - This is filled with either the saved settings or the POST values.
	 *                    To install this Extension with the default settings saved, use the & reference.
	 */
	public function settings(&$settings)
	{
		$defaults = array(
			'estimator' => '',
		);

		$settings += $defaults;

		$data['settings'] = $settings;

		//Render
		$this->render('extension/total/shipping', $data);
	}
}
