<?php
class Catalog_Controller_Area_Left extends Controller
{
	public function index()
	{
		//Load Blocks associated with this position
		$blocks = $this->block->getInstancesFor('left');

		$data = array(
			'blocks' => array(),
		);

		foreach ($blocks as $key => $block) {
			$settings               = $block['settings'] + $block['profile'];
			$data['blocks'][] = $this->block->render($key, array(), $settings);
		}

		$this->render('area/left', $data);
	}
}
