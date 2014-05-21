<?php
class App_Controller_Product_SearchResults extends Controller
{
	public function index()
	{
		$post = $_POST;
		if (!isset($post['action']) || $post['action'] != 'betty_search') {
			echo _l("No search request was made");
			exit;
		}

		$data['search_category'] = "";
		$data['search_country']  = "";
		$data['search_color']    = '';

		$search_query = array();
		foreach ($post as $key => $p) {
			if (!empty($p) && preg_match('/^search_/', $key)) {
				$search_query[preg_replace('/search_/', '', $key)] = $p;
			}
		}

		$results = $this->Model_Catalog_Product->getProductSearchResults($search_query);

		foreach ($results as $key => $r) {
			$results[$key]['image']       = $this->image->resize($r['image'], 130, 130);
			$results[$key]['href']        = site_url('product/product', "product_id=$r[product_id]");
			$results[$key]['price']       = $this->currency->format($r['price']);
			$results[$key]['special']     = $r['special'] > 0 ? $this->currency->format($r['special']) : null;
			$results[$key]['description'] = htmlspecialchars_decode($r['description']);
		}

		$data['results'] = $results;

		$this->response->setOutput($this->render('product/search_results', $data));
	}
}
