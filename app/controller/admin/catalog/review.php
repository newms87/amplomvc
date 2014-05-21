<?php
class App_Controller_Admin_Catalog_Review extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Reviews"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("Reviews"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Catalog_Review->addReview($_POST);

			$this->message->add('success', _l("Success: You have modified reviews!"));

			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			redirect('catalog/review', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->document->setTitle(_l("Reviews"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Catalog_Review->editReview($_GET['review_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified reviews!"));

			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			redirect('catalog/review', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("Reviews"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $review_id) {
				$this->Model_Catalog_Review->deleteReview($review_id);
			}

			$this->message->add('success', _l("Success: You have modified reviews!"));

			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			redirect('catalog/review', $url);
		}

		$this->getList();
	}

	private function getList()
	{
		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'r.date_added';
		}

		if (isset($_GET['order'])) {
			$order = $_GET['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Reviews"), site_url('catalog/review', $url));

		$data['insert'] = site_url('catalog/review/insert', $url);
		$data['delete'] = site_url('catalog/review/delete', $url);

		$data['reviews'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * option('config_admin_limit'),
			'limit' => option('config_admin_limit')
		);

		$review_total = $this->Model_Catalog_Review->getTotalReviews();

		$results = $this->Model_Catalog_Review->getReviews($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => site_url('catalog/review/update', 'review_id=' . $result['review_id'] . $url)
			);

			$data['reviews'][] = array(
				'review_id'  => $result['review_id'],
				'name'       => $result['name'],
				'author'     => $result['author'],
				'rating'     => $result['rating'],
				'status'     => ($result['status'] ? _l("Enabled") : _l("Disabled")),
				'date_added' => $this->date->format($result['date_added'], 'short'),
				'selected'   => isset($_GET['selected']) && in_array($result['review_id'], $_GET['selected']),
				'action'     => $action
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if ($this->session->has('success')) {
			$data['success'] = $this->session->get('success');

			$this->session->remove('success');
		} else {
			$data['success'] = '';
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$data['sort_product']    = site_url('catalog/review', 'sort=pd.name' . $url);
		$data['sort_author']     = site_url('catalog/review', 'sort=r.author' . $url);
		$data['sort_rating']     = site_url('catalog/review', 'sort=r.rating' . $url);
		$data['sort_status']     = site_url('catalog/review', 'sort=r.status' . $url);
		$data['sort_date_added'] = site_url('catalog/review', 'sort=r.date_added' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $review_total;
		$data['pagination'] = $this->pagination->render();

		$data['sort']  = $sort;
		$data['order'] = $order;

		$this->response->setOutput($this->render('catalog/review_list', $data));
	}

	private function getForm()
	{
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['product'])) {
			$data['error_product'] = $this->error['product'];
		} else {
			$data['error_product'] = '';
		}

		if (isset($this->error['author'])) {
			$data['error_author'] = $this->error['author'];
		} else {
			$data['error_author'] = '';
		}

		if (isset($this->error['text'])) {
			$data['error_text'] = $this->error['text'];
		} else {
			$data['error_text'] = '';
		}

		if (isset($this->error['rating'])) {
			$data['error_rating'] = $this->error['rating'];
		} else {
			$data['error_rating'] = '';
		}

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Reviews"), site_url('catalog/review', $url));

		if (!isset($_GET['review_id'])) {
			$data['action'] = site_url('catalog/review/insert', $url);
		} else {
			$data['action'] = site_url('catalog/review/update', 'review_id=' . $_GET['review_id'] . $url);
		}

		$data['cancel'] = site_url('catalog/review', $url);

		if (isset($_GET['review_id']) && !$this->request->isPost()) {
			$review_info = $this->Model_Catalog_Review->getReview($_GET['review_id']);
		}

		if (isset($_POST['product_id'])) {
			$data['product_id'] = $_POST['product_id'];
		} elseif (!empty($review_info)) {
			$data['product_id'] = $review_info['product_id'];
		} else {
			$data['product_id'] = '';
		}

		if (isset($_POST['product'])) {
			$data['product'] = $_POST['product'];
		} elseif (!empty($review_info)) {
			$data['product'] = $review_info['product'];
		} else {
			$data['product'] = '';
		}

		if (isset($_POST['author'])) {
			$data['author'] = $_POST['author'];
		} elseif (!empty($review_info)) {
			$data['author'] = $review_info['author'];
		} else {
			$data['author'] = '';
		}

		if (isset($_POST['text'])) {
			$data['text'] = $_POST['text'];
		} elseif (!empty($review_info)) {
			$data['text'] = $review_info['text'];
		} else {
			$data['text'] = '';
		}

		if (isset($_POST['rating'])) {
			$data['rating'] = $_POST['rating'];
		} elseif (!empty($review_info)) {
			$data['rating'] = $review_info['rating'];
		} else {
			$data['rating'] = '';
		}

		if (isset($_POST['status'])) {
			$data['status'] = $_POST['status'];
		} elseif (!empty($review_info)) {
			$data['status'] = $review_info['status'];
		} else {
			$data['status'] = '';
		}

		//Ajax Urls
		$data['url_product_autocomplete'] = site_url('catalog/product/autocomplete');

		$this->response->setOutput($this->render('catalog/review_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'catalog/review')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify reviews!");
		}

		if (!$_POST['product_id']) {
			$this->error['product'] = _l("Product required!");
		}

		if ((strlen($_POST['author']) < 3) || (strlen($_POST['author']) > 64)) {
			$this->error['author'] = _l("Author must be between 3 and 64 characters!");
		}

		if (strlen($_POST['text']) < 1) {
			$this->error['text'] = _l("Review Text must be at least 1 character!");
		}

		if (!isset($_POST['rating'])) {
			$this->error['rating'] = _l("Review rating required!");
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'catalog/review')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify reviews!");
		}

		return empty($this->error);
	}
}
