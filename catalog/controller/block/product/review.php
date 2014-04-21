<?php
class Catalog_Controller_Block_Product_Review extends Controller
{
	public function single()
	{
		$data['reviews'] = _l("%s reviews", (int)$product_info['reviews']);

		$data['rating'] = (int)$product_info['rating'];
	}

	public function review()
	{
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$data['reviews'] = array();

		$review_total = $this->Model_Catalog_Review->getTotalReviewsByProductId($_GET['product_id']);
		$results      = $this->Model_Catalog_Review->getReviewsByProductId($_GET['product_id'], ($page - 1) * 5, 5);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => $result['text'],
				'rating'     => (int)$result['rating'],
				'reviews'    => sprintf(_l("%s reviews"), (int)$review_total),
				'date_added' => $this->date->format($result['date_added'], 'short'),
			);
		}

		$review_status = $this->config->get('config_review_status');

		$data['review_status'] = $review_status;

		if ($review_status) {
			$data['review_count'] = $this->Model_Catalog_Review->getTotalReviewsByProductId($product_info['product_id']);

			$data['reviews'] = _l("%s reviews", (int)$product_info['reviews']);

			$data['rating'] = (int)$product_info['rating'];
		}


		$this->pagination->init();
		$this->pagination->total  = $review_total;
		$data['pagination'] = $this->pagination->render();

		$this->response->setOutput($this->render('product/review', $data));
	}

	public function write()
	{
		$json = array();

		if ($this->request->isPost()) {
			if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 25)) {
				$json['error'] = _l("Warning: Review Name must be between 3 and 25 characters!");
			}

			if ((strlen($_POST['text']) < 25) || (strlen($_POST['text']) > 1000)) {
				$json['error'] = _l("Warning: Review Text must be between 25 and 1000 characters!");
			}

			if (!$_POST['rating']) {
				$json['error'] = _l("Warning: Please select a review rating!");
			}

			if (!$this->captcha->validate($_POST['captcha'])) {
				$json['error'] = _l("Warning: Verification code does not match the image!");
			}

			if (!isset($json['error'])) {
				$this->Model_Catalog_Review->addReview($_GET['product_id'], $_POST);

				$json['success'] = _l("Thank you for your review. It has been submitted to the webmaster for approval.");
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function captcha()
	{
		$this->captcha->generate();
	}
}
