<?php
class ControllerCommonFooter extends Controller {   
	protected function index() {
		$this->template->load('common/footer');

		$this->load->language('common/footer');
		
		$this->render();
  	}
}