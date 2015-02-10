<?php

class App_Controller_Area_Left extends Controller
{
	public function index()
	{
		//Load Blocks associated with this position
		$instances = $this->area->getBlocks('left');

		$blocks = array();

		foreach ($instances as $name => $instance) {
			$blocks[] = block($instance['path'], $name);
		}

		$data = array(
			'blocks' => $blocks,
		);

		//Render
		$this->render('area/left', $data);
	}
}
