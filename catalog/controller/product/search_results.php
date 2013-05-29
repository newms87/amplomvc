<?php
class ControllerProductSearchResults extends Controller 
{
	public function index()
	{
		$this->template->load('product/search_results');
		
		$this->language->load('product/search_results');
		
		$post = $_POST;
		if (!isset($post['action']) || $post['action'] != 'betty_search') {
			echo $this->_('no_search');
			exit;
		}
		
		$this->data['search_category'] = "";
		$this->data['search_country'] = "";
		$this->data['search_color'] = '';

		$search_query = array();
		foreach ($post as $key=>$p) {
			if(!empty($p) && preg_match('/^search_/',$key))
				$search_query[preg_replace('/search_/','',$key)] = $p;
		}
		
		$results = $this->model_catalog_product->getProductSearchResults($search_query);
		
		foreach ($results as $key=>$r) {
			$results[$key]['image'] =  $this->image->resize($r['image'], 130, 130);
			$results[$key]['href'] = $this->url->link('product/product', "product_id=$r[product_id]");
			$results[$key]['price'] = $this->currency->format($r['price']);
			$results[$key]['special'] = $r['special'] > 0 ?$this->currency->format($r['special']):null;
			$results[$key]['description'] = htmlspecialchars_decode($r['description']);
		}

		$this->data['results'] = $results;
		
		$this->response->setOutput($this->render());
  	}
}