#<?php
//=====
class Admin_Controller_Setting_Setting extends Controller 
{
//.....
	public function index()
	{
//.....
		$defaults = array(
//-----
//>>>>> {php}
			'config_show_collection_image' => 1,
			'config_show_collection_description' => 1,
			'config_image_collection_width' => 80,
			'config_image_collection_height' => 80,
//-----
//=====
		);
//.....
	}
//.....
}
//-----