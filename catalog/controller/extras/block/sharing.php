<?php	
class ControllerExtrasBlockSharing extends Controller {
		
	public function index() {
		$this->language->load('extras/block/sharing');
		
		$this->template->load('extras/block/sharing');
		
		$this->render();
	}
}