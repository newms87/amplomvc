<?php
class App_Model_Setting_Setting extends Model
{
	public function getWidgets()
	{
		$widgets = array();

		$files = glob(DIR_SITE . 'app/controller/admin/setting/*');

		if ($files) {
			$order = 0;

			foreach ($files as $file) {
				$directives = $this->tool->getFileCommentDirectives($file);

				if (empty($directives['title'])) {
					continue;
				}

				$widget['title'] = _l($directives['title']);

				if (!empty($directives['icon'])) {
					$widget['icon'] = $this->theme->getUrl('image/' . $directives['icon']);
				}

				if (empty($widget['icon'])) {
					$widget['icon'] = $this->theme->getUrl('image/admin_settings.png');
				}

				if (!empty($directives['path'])) {
					$query = !empty($directives['query']) ? $directives['query'] : '';
					$widget['url'] = site_url($directives['path'], $query);
				} else {
					$widget['url'] = site_url('setting/' . str_replace('.php','',basename($file)));
				}

				$widget['sort_order'] = isset($directives['order']) ? (float)$directives['order'] : $order++;

				$widgets[] = $widget;
			}
		}

		usort($widgets, function($a,$b) { return $a['sort_order'] > $b['sort_order']; });

		return $widgets;
	}
}
