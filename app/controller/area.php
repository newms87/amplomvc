<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the root directory.
 */

class App_Controller_Area extends Controller
{
	public function index($area)
	{
		//Load Blocks associated with this position
		$instances = $this->area->getBlocks($area);

		$blocks = array();

		foreach ($instances as $name => $instance) {
			$blocks[] = block($instance['path'], $name);
		}

		$data = array(
			'blocks' => $blocks,
		);

		//Render
		$this->render('area/' . $area, $data);
	}

	public function above()
	{
		$this->index('above');
	}

	public function below()
	{
		$this->index('below');
	}

	public function top()
	{
		$this->index('top');
	}

	public function bottom()
	{
		$this->index('bottom');
	}

	public function left()
	{
		$this->index('left');
	}

	public function right()
	{
		$this->index('right');
	}
}
