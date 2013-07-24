<?php
class Catalog_Controller_Block_Product_Review extends Controller
{
	public function single()
	{
		$this->data['reviews'] = $this->_('text_reviews', (int)$product_info['reviews']);
			
		$this->data['rating'] = (int)$product_info['rating'];
	}
	
	public function review()
	{
		$this->template->load('product/review');

		$this->language->load('product/product');

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		$this->data['reviews'] = array();
		
		$review_total = $this->Model_Catalog_Review->getTotalReviewsByProductId($_GET['product_id']);
		$results = $this->Model_Catalog_Review->getReviewsByProductId($_GET['product_id'], ($page - 1) * 5, 5);
				
		foreach ($results as $result) {
			$this->data['reviews'][] = array(
				'author'	=> $result['author'],
				'text'		=> $result['text'],
				'rating'	=> (int)$result['rating'],
				'reviews'	=> sprintf($this->_('text_reviews'), (int)$review_total),
				'date_added' => $this->date->format($result['date_added'], $this->language->getInfo('date_format_short')),
			);
		}
		
		$review_status = $this->config->get('config_review_status');
		
		$this->data['review_status'] = $review_status;
		
		if ($review_status) {
			$this->_('tab_review', $this->Model_Catalog_Review->getTotalReviewsByProductId($product_info['product_id']));
			
			$this->data['reviews'] = $this->_('text_reviews', (int)$product_info['reviews']);
			
			$this->data['rating'] = (int)$product_info['rating'];
		}
		
		
		$this->pagination->init();
		$this->pagination->total = $review_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->response->setOutput($this->render());
	}
	
	public function write()
	{
		$this->language->load('product/product');
		
		$json = array();
		
		if ($this->request->isPost()) {
			if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 25)) {
				$json['error'] = $this->_('error_name');
			}
			
			if ((strlen($_POST['text']) < 25) || (strlen($_POST['text']) > 1000)) {
				$json['error'] = $this->_('error_text');
			}
	
			if (!$_POST['rating']) {
				$json['error'] = $this->_('error_rating');
			}
	
			if (!$this->captcha->validate($_POST['captcha'])) {
				$json['error'] = $this->_('error_captcha');
			}
				
			if (!isset($json['error'])) {
				$this->Model_Catalog_Review->addReview($_GET['product_id'], $_POST);
				
				$json['success'] = $this->_('text_success');
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function captcha()
	{
		$this->captcha->generate();
	}
}