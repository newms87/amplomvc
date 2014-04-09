<?php
class Admin_Controller_Filemanager_Filemanager extends Controller
{
	public function get_thumb()
	{
		$width = !empty($_GET['width']) ? $_GET['width'] : $this->config->get('config_image_admin_thumb_width');
		$height = !empty($_GET['height']) ? $_GET['height'] : $this->config->get('config_image_admin_thumb_height');

		$this->response->setOutput($this->image->resize($_GET['image'], $width, $height));
	}
}
