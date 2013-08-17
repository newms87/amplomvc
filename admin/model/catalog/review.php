<?php
class Admin_Model_Catalog_Review extends Model
{
	//TODO: Reviews should not invalidate the product!
	public function addReview($data)
	{
		$data['date_added'] = $this->date->now();
		$data['text']       = strip_tags($data['text']);

		$review_id = $this->insert('review', $data);

		return $review_id;
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
			$order = $this->extract_order($order);
			$limit = $this->extract_limit($limit);
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

	public function getTotalReviews($data = array())
	{
		return $this->getReviews($data, '', true);
	}

	public function getTotalReviewsAwaitingApproval()
	{
		return $this->queryVar("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review WHERE status = '0'");
	}
}
