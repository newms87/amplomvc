<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * Title: Theme Settings
 * Icon: theme.png
 * Order: 3
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class App_Controller_Admin_Settings_Theme extends Controller
{
	public function index()
	{
		//Page Head
		set_page_info('title', _l("Theme Settings"));

		//Breadcrumbs
		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Settings"), site_url('admin/settings/setting'));
		breadcrumb(_l("Theme"), site_url('admin/settings/theme'));

		//Load Data or Defaults
		$theme_configs = $this->theme->loadTheme();

		$defaults = array(
			'configs'    => array(),
			'stylesheet' => '',
		);

		$settings = $_POST + $theme_configs + $defaults;

		$settings['configs'] += $theme_configs['configs'];

		//Render
		output($this->render('settings/theme', $settings));
	}

	public function save()
	{
		$theme = $this->config->load('config', 'site_theme');

		if (!$theme) {
			$theme = AMPLO_DEFAULT_THEME;
		}

		//Save Settings
		$this->theme->saveTheme($theme, $_POST['configs'], $_POST['stylesheet']);

		if ($this->theme->hasError()) {
			message('error', $this->theme->fetchError());
		} else {
			message('success', _l("You have successfully updated the Theme Settings!"));
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/settings');
		}
	}

	public function restore_defaults()
	{
		if ($this->theme->restore()) {
			message('success', _l("Store theme has been restored to the default values"));
		} else {
			message('error', $this->theme->fetchError());
		}

		if ($this->is_ajax) {
			output_message();
		} else {
			redirect('admin/settings/theme');
		}
	}
}
