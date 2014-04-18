<?php
class Catalog_Controller_Area_Right extends Controller
{
	public function index()
	{
		//Load Blocks associated with this position
		$blocks = $this->block->getAreaInstances('right');

		$data = array(
			'blocks' => array(),
		);

		foreach ($blocks as $key => $block) {
			$settings               = $block['settings'] + $block['profile'];
			$data['blocks'][] = $this->block->render($key, array(), $settings);
		}

		$this->render('area/right', $data);
	}
}
