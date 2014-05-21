<?php
class App_Model_Catalog_Review extends Model
{
	public function addReview($product_id, $data)
	{
		$data['date_added'] = $this->date->now();
		$data['text']       = strip_tags($data['text']);
		$data['product_id'] = $product_id;

		return $this->insert('review', $data);
	}

	public function editReview($review_id, $data)
	{
		$data['date_added'] = $this->date->now();
		$data['text']       = strip_tags($data['text']);

		$this->update('review', $data, $review_id);
	}

	public function deleteReview($review_id)
	{
		$this->delete('review', $review_id);
	}

	public function getAverageRating($product_id)
	{
		return $this->queryVar("SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review WHERE status = '1' AND product_id = '" . (int)$product_id . "' GROUP BY product_id");
	}

	public function getReview($review_id)
	{
		return $this->queryRow("SELECT *, p.name as product FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id=r.product_id) WHERE r.review_id = '" . (int)$review_id . "'");
	}

	public function getReviews($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = 'COUNT(*) as total';
		} elseif (empty($select)) {
			$select = 'r.review_id, p.name, r.author, r.rating, r.status, r.date_added';
		}

		//From
		$from = DB_PREFIX . "review r" .
			" LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id=r.product_id)";

		//Where
		$where = '1';

		//Order and Limit
		if (!$total) {
			$order = $this->extractOrder($data);
			$limit = $this->extractLimit($data);
		} else {
			$order = '';
			$limit = '';
		}

		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";

		$result = $this->query($query);

		if ($total) {
			return $result->row['total'];
		}

		return $result->rows;
	}

	public function getReviewsByProductId($product_id, $start = 0, $limit = 20)
	{
		$reviews = $this->queryRows("SELECT r.review_id, r.author, r.rating, r.text, p.product_id, pd.name, p.price, p.image, r.date_added FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' ORDER BY r.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		$this->translation->translateAll('product', 'product_id', $reviews);

		return $reviews;
	}

	public function getTotalReviews($data = array())
	{
		return $this->getReviews($data, '', true);
	}

	public function getTotalReviewsAwaitingApproval()
	{
		return $this->queryVar("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review WHERE status = '0'");
	}

	public function getTotalReviewsByProductId($product_id)
	{
		return $this->queryVar("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1'");
	}
}
