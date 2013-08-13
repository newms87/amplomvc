<?php
class Admin_Controller_Shipping_Flat extends Controller
{
	
	public function index()
	{
		$this->template->load('shipping/flat');

		$this->language->load('shipping/flat');

		$this->document->setTitle($this->_('head_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('shipping_flat', $_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('extension/shipping'));
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_shipping'), $this->url->link('extension/shipping'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('shipping/flat'));

		$this->data['action'] = $this->url->link('shipping/flat');
		$this->data['cancel'] = $this->url->link('extension/shipping');
		
		$flat_info = $this->Model_Setting_Setting->getSetting('shipping_flat');
		
		$defaults = array(
			'flat_title' => '',
			'flat_rates' => array(),
			'flat_status' => 1,
			'flat_sort_order' => 0,
		);
		
		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($flat_info[$key])) {
				$this->data[$key] = $flat_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		$this->data['data_tax_classes'] = $this->Model_Localisation_TaxClass->getTaxClasses();
		
		$this->data['data_geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();
								
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'shipping/flat')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (empty($_POST['flat_rates'])) {
			$this->error['flat_rates'] = $this->_('error_flat_rates');
		}
		else {
			foreach ($_POST['flat_rates'] as $key => $rate) {
				if (empty($rate['title'])) {
					$this->error["flat_rates[$key][title]"] = $this->_('error_title');
				}
				else {
					$_POST['flat_rates'][$key]['method'] = $this->tool->getSlug($rate['title']);
					
					foreach ($_POST['flat_rates'] as $key2 => $rate2) {
						if ($rate2['method'] == $rate['title']) {
							$_POST['flat_rates'][$key]['method'] .= "_" . uniqid();
						}
					}
				}
			
				switch($rate['rule']['type']){
					case 'item_qty':
						if (!preg_match("/^[0-9]+,?[0-9]*$/", $rate['rule']['value'])) {
							$this->error["flat_rates[$key][rule][value]"] = $this->_('error_rule_value');
						}
						else {
							if (preg_match("/^[0-9]+$/", $rate['rule']['value'])) {
								$_POST['flat_rates'][$key]['rule']['value'] .= ",0";
							}
						}
						break;
					case 'weight':
						break;
					default:
						break;
				}
			}
		}
		
		return $this->error ? false : true;
	}
}