<?php
//TODO: Move this to Block widget/limit

class Sort extends Library
{
	//TODO: Move this to the admin panel
	static $limits = array(
		5   => '5',
		10  => '10',
		20  => '20',
		50  => '50',
		100 => '100',
		0   => 'all'
	);

	public function renderLimits($settings = array())
	{
		$defaults = array(
			'template'   => 'block/widget/limit',
			'limits'     => self::$limits,
			'path'       => $this->route->getPath(),
			'limit_text' => '(see more)',
			'limit'      => _get('limit', 0),
		);

		$settings += $defaults;

		$limit = (int)$settings['limit'];

		$template_file = $this->theme->getFile($settings['template']);

		if (!$template_file) {
			trigger_error(_l("%s(): Limit template %s was found!", __METHOD__, $template_file));

			return;
		}

		//Set limit for pagination compatibility
		$_GET['limit'] = $limit;

		$settings['limit_url'] = site_url($settings['path'], $this->url->getQueryExclude('limit', 'page') . '&limit=');
		$settings['limit']     = $limit;

		$settings['show_more'] = $settings['limit_url'] . ($limit + option('limit_more_count', 10));
		extract($settings);

		ob_start();

		include(_mod($template_file));

		return ob_get_clean();
	}
}
