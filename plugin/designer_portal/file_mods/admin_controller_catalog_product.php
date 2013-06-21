//=====
<?php
class _AdminControllerCatalogProduct extends Controller 
{
//.....
	public function insert()
	{
//-----
//>>>>> {php}
		if ($this->user->isDesigner() && $this->request->isPost()) {
			$pd = reset($_POST['product_description']);
			$_POST['keyword'] = $this->Model_Catalog_Product->generate_url(false, $pd['name']);
		}
//-----
//=====
	}
//.....
	public function update()
	{
//-----
//>>>>> {php}
		if ($this->user->isDesigner() && $this->request->isPost()) {
			$pd = reset($_POST['product_description']);
			$_POST['keyword'] = $this->Model_Catalog_Product->generate_url(false, $pd['name']);
		}
//-----
//=====
	}
//.....
	private function getList()
	{
//-----
//<<<<<
		$this->template->load('catalog/product_list');
//-----
//>>>>> {php}
		if ($this->user->isDesigner()) {
			$this->template->load('catalog/product_list_restricted');
		}
		else {
			$this->template->load('catalog/product_list');
		}
//-----
//>>>>> {before} {php}
		if ($this->user->isDesigner()) {
			$designers =$this->Model_User_User->getUserDesigners($this->user->getId());
			
			$data['filter_manufacturer_id'] = array();
			
			foreach ($designers as $d) {
				$data['filter_manufacturer_id'][] = $d['designer_id'];
			}
		}
//-----
//=====
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
//-----
//=====
		$this->data['category_list'] = array(''=>'');
		foreach ($this->Model_Catalog_Category->getCategories(null) as $cat) {
			$this->data['category_list'][$cat['category_id']] = $cat['name'];
		}
//-----
//>>>>>
		$restrict_list = array();
		
		if ($this->user->isDesigner()) {
			$r_list = $this->Model_User_User->getUserDesigners($this->user->getId());
			
			foreach ($r_list as $r) {
				$restrict_list[] = $r['designer_id'];
			}
			
			if (empty($restrict_list)) {
				$restrict_list = array(0);
			}
		}
//-----
//=====
		foreach ($manufacturers as $manufacturer) {
//-----
//>>>>> {php}
			if(!empty($restrict_list) && !in_array($manufacturer['manufacturer_id'],$restrict_list))continue;
//-----
//=====
		}
//.....
	}
//.....
	private function getForm()
	{
//-----
//<<<<<
		$this->template->load('catalog/product_form');
//-----
//>>>>> {php}
		if ($this->user->isDesigner()) {
			$this->template->load('catalog/product_form_restricted');
		}
		else {
			$this->template->load('catalog/product_form');
		}
//-----
//=====
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/product'));
//-----
//>>>>> {php}
		if ($this->user->isDesigner()) {
			$this->language->set('entry_model', $this->_("entry_model_designer"));
			$this->language->set('entry_shipping_ret', $this->_('entry_shipping_ret_designer',$this->url->link('catalog/manufacturer')));
		}
//-----
//=====
		if ($product_id && !$this->request->isPost()) {
			$product_info = $this->Model_Catalog_Product->getProduct($product_id);
		}
//-----
//>>>>> {php}
		elseif ($product_id) {
			$_POST['editable'] = $this->Model_Catalog_Product->isEditable($product_id);
		}
//-----
//=====
		$defaults = array(
//-----
//>>>>> {php}
			'editable'=>1,
//-----
//=====
			);
//.....
		foreach ($defaults as $d=>$default) {
			if (isset($_POST[$d]))
				$this->data[$d] = $_POST[$d];
			elseif (isset($product_info[$d]))
				$this->data[$d] = $product_info[$d];
			elseif(!$product_id)
				$this->data[$d] = $default;
		}
//-----
//>>>>> {php}
		if (!$this->data['editable']) {
			$this->_('text_not_editable',$this->data['model'],"Active%20Product%20Modification%20Request");
		}
//-----
//<<<<<
		if (!isset($this->data['date_available'])) {
//-----
		} //ignore
//>>>>> {php}
		if (!isset($this->data['date_available']) && !$this->user->isDesigner()) {
//-----
		} //ignore
//<<<<<
		$this->data['manufacturers'] = array(0=>$this->_('text_none'));
//-----
//>>>>> {php}
		$restrict_list = array();
		
		if ($this->user->isDesigner()) {
			$this->data['manufacturers'] = array();
			
			$r_list = $this->Model_User_User->getUserDesigners($this->user->getId());
			
			foreach ($r_list as $r) {
				$restrict_list[] = $r['designer_id'];
			}
			if (empty($restrict_list)) {
				$restrict_list = array(0);
			}
		}
		else {
			$this->data['manufacturers'] = array(0=>$this->_('text_none'));
		}
//-----
//=====
		foreach ($manufacturers as $man) {
//-----
//>>>>> {php}
			if(!empty($restrict_list) && !in_array($man['manufacturer_id'],$restrict_list))continue;
//-----
//=====
		}
//.....
	}
//.....
	
	private function validateForm()
	{
//-----
//>>>>> {php}
		if ($this->user->isDesigner() && isset($_GET['product_id']) && !$this->Model_Catalog_Product->isEditable($_GET['product_id'])) {
			$this->session->data['warning'] = $this->_('warning_not_editable');
			$this->url->redirect($this->url->link('catalog/product'));
		}
//-----
//=====
		if ((strlen($_POST['model']) < 1) || (strlen($_POST['model']) > 64)) {
//-----
//<<<<<
			$this->error['model'] = $this->_('error_model');
//-----
//>>>>> {php}
			if (!$this->user->isDesigner()) {
				$this->error['model'] = $this->_('error_model');
			}
//-----
//=====
		}
//.....
	}
//.....
	private function validateDelete()
	{
//-----
//>>>>> {php}
		if ($this->user->isDesigner() && !$this->Model_Catalog_Product->isEditable($_GET['product_id'])) {
			$this->session->data['warning'] = $this->_('warning_not_editable');
			$this->url->redirect($this->url->link('catalog/product'));
		}
//-----
//=====
	}
//.....
}
//-----