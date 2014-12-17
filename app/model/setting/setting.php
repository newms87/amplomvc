<?php

class App_Model_Settings extends Model
{
	public function saveGeneral($settings)
	{
		if (empty($settings['site_name']) || !validate($settings['site_name'], 2, 128)) {
			$this->error['site_name'] = _l("Site Name must be between 2 and 128 characters!");
		}

		if (empty($settings['site_email']) || !validate('email', $settings['site_email'])) {
			$this->error['site_email'] = _l("The Site Email does not appear to be valid!");
		}

		if (isset($settings['site_email_support']) && !validate('email', _post('site_email_support'))) {
			$this->error['site_email_support'] = _l("The Support Email %s does not appear to be valid.", $settings['site_email_support']);
		}

		if (isset($settings['site_email_error']) && !validate('email', _post('site_email_error'))) {
			$this->error['site_email_error'] = _l("The Error Email %s does not appear to be valid.", $settings['site_email_error']);
		}

		if ($this->error) {
			return false;
		}

		if (empty($settings['site_title'])) {
			$settings['site_title'] = $settings['site_name'];
		}

		$settings['admin_list_limit'] = max(0, (int)$settings['admin_list_limit']);
		$settings['site_list_limit']  = max(0, (int)$settings['site_list_limit']);

		$this->config->saveGroup('general', $settings);
	}

	public function getWidgets()
	{
		$widgets = array();

		$files = glob(DIR_SITE . 'app/controller/admin/settings/*');

		if ($files) {
			$order = 0;

			foreach ($files as $file) {
				$directives = get_comment_directives($file);

				if (empty($directives['title'])) {
					continue;
				}

				$widget['title'] = _l($directives['title']);

				if (!empty($directives['icon'])) {
					$widget['icon'] = $this->theme->getUrl('image/settings/' . $directives['icon']);
				}

				if (empty($widget['icon'])) {
					$widget['icon'] = $this->theme->getUrl('image/settings/admin.png');
				}

				if (!empty($directives['path'])) {
					$query         = !empty($directives['query']) ? $directives['query'] : '';
					$widget['url'] = site_url($directives['path'], $query);
				} else {
					$widget['url'] = site_url('admin/settings/' . str_replace('.php', '', basename($file)));
				}

				$widget['sort_order'] = isset($directives['order']) ? (float)$directives['order'] : $order++;

				$widgets[] = $widget;
			}
		}

		usort($widgets, function ($a, $b) {
			return $a['sort_order'] > $b['sort_order'];
		});

		return $widgets;
	}
}
