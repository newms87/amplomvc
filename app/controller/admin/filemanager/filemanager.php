<?php
class App_Controller_Admin_Filemanager_Filemanager extends Controller
{
	public function get_thumb()
	{
		$width = _get('width', option('config_image_admin_thumb_width'));
		$height = _get('height', option('config_image_admin_thumb_height'));

		output(image($_GET['image'], $width, $height));
	}
}
