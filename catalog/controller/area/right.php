<?php
class Catalog_Controller_Area_Right extends Controller
{
	public function index()
	{
		//Load Blocks associated with this position
		$instances = $this->area->getBlocks('right');

		$blocks = array();

		foreach ($instances as $name => $instance) {
			$blocks[] = $this->block->render($instance['path'], $name);
		}

		$data = array(
			'blocks' => $blocks,
		);

		//Render
		$this->render('area/right', $data);
	}
}
