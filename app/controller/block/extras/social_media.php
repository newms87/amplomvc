<?php
class App_Controller_Block_Extras_SocialMedia extends Controller
{
	public function build($settings, $type = 'standard')
	{
		if (isset($settings['networks'])) {
			//TODO: MOVE the size settings to the block profile
			if (!isset($settings['width'])) {
				$settings['width'] = $settings['height'] = 25;
			}

			foreach ($settings['networks'] as &$network) {
				$network['thumb'] = $this->image->resize($network['icon'], $settings['width'], $settings['height']);
			}
			unset($network);

		} else {
			return; // we return without rendering because there is nothing to output.
		}

		switch ($type) {
			case 'standard':
				$template = 'block/extras/my_networks';
				break;
			default:
				$template = 'block/extras/my_networks';
				break;
		}

		$this->render($template, $settings);
	}
}
