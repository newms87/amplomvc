<?php
class Admin_Model_Setting_Setting extends Model
{
	public function getWidgets()
	{
		$widgets = array();

		$files = glob(DIR_APPLICATION . 'controller/setting/*');

		if ($files) {
			$order = 0;

			foreach ($files as $file) {
				$directives = $this->tool->getFileCommentDirectives($file);

				if (empty($directives['title'])) {
					continue;
				}

				$widget['title'] = _l($directives['title']);

				if (!empty($directives['icon']) && is_file(DIR_THEME_IMAGE . $directives['icon'])) {
					$widget['icon'] = HTTP_THEME_IMAGE . $directives['icon'];
				} else {
					$widget['icon'] = HTTP_THEME_IMAGE . 'admin_settings.png';
				}

				$widget['url'] = $this->url->link('setting/' . str_replace('.php','',basename($file)));

				$widget['sort_order'] = isset($directives['order']) ? (float)$directives['order'] : $order++;

				$widgets[] = $widget;
			}
		}

		usort($widgets, function($a,$b) { return $a['sort_order'] > $b['sort_order']; });

		return $widgets;
	}
}
