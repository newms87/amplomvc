<?php
class Catalog_Model_Catalog_Review extends Model 
{
	public function addReview($product_id, $data)
	{
		$data = array(
			'author' => $data['name'],
			'customer_id' => $this->customer->getId(),
			'product_id' => $product_id,
			'date_added' => $this->date->now(),
		);
		
		$review_id = $this->insert('review', $data);
		
		return $review_id;
	}
		
	public function getReviewsByProductId($product_id, $start = 0, $limit = 20)
	{
		$reviews = $this->queryRows("SELECT r.review_id, r.author, r.rating, r.text, p.product_id, pd.name, p.price, p.image, r.date_added FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' ORDER BY r.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
		
		$this->translation->translate_all('product', 'product_id', $reviews);
		
		return $reviews;
	}
	
	public function getAverageRating($product_id)
	{
		return $this->queryVar("SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review WHERE status = '1' AND product_id = '" . (int)$product_id . "' GROUP BY product_id");
	}
	
	public function getTotalReviews()
	{
		return $this->queryVar("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) WHERE p.date_available <= NOW() AND p.status = '1' AND r.status = '1'");
	}

	public function getTotalReviewsByProductId($product_id)
	{
		return $this->queryVar("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1'");
	}
}