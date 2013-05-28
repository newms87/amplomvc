<?php
class ControllerCatalogReview extends Controller {
	
 
	public function index() {
		$this->load->language('catalog/review');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}

	public function insert() {
		$this->load->language('catalog/review');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_review->addReview($_POST);
			
			$this->message->add('success', $this->_('text_success'));

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
						
			$this->url->redirect($this->url->link('catalog/review', $url));
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('catalog/review');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_review->editReview($_GET['review_id'], $_POST);
			
			$this->message->add('success', $this->_('text_success'));

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
						
			$this->url->redirect($this->url->link('catalog/review', $url));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/review');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $review_id) {
				$this->model_catalog_review->deleteReview($review_id);
			}

			$this->message->add('success', $this->_('text_success'));

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
						
			$this->url->redirect($this->url->link('catalog/review', $url));
		}

		$this->getList();
	}

	private function getList() {
		$this->template->load('catalog/review_list');

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

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/review', $url));

		$this->data['insert'] = $this->url->link('catalog/review/insert', $url);
		$this->data['delete'] = $this->url->link('catalog/review/delete', $url);

		$this->data['reviews'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$review_total = $this->model_catalog_review->getTotalReviews();
	
		$results = $this->model_catalog_review->getReviews($data);
 
		foreach ($results as $result) {
			$action = array();
						
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('catalog/review/update', 'review_id=' . $result['review_id'] . $url)
			);
						
			$this->data['reviews'][] = array(
				'review_id'  => $result['review_id'],
				'name'		=> $result['name'],
				'author'	=> $result['author'],
				'rating'	=> $result['rating'],
				'status'	=> ($result['status'] ? $this->_('text_enabled') : $this->_('text_disabled')),
				'date_added' => $this->tool->format_datetime($result['date_added'], $this->language->getInfo('date_format_short')),
				'selected'	=> isset($_POST['selected']) && in_array($result['review_id'], $_POST['selected']),
				'action'	=> $action
			);
		}
	
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
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
		
		$this->data['sort_product'] = $this->url->link('catalog/review', 'sort=pd.name' . $url);
		$this->data['sort_author'] = $this->url->link('catalog/review', 'sort=r.author' . $url);
		$this->data['sort_rating'] = $this->url->link('catalog/review', 'sort=r.rating' . $url);
		$this->data['sort_status'] = $this->url->link('catalog/review', 'sort=r.status' . $url);
		$this->data['sort_date_added'] = $this->url->link('catalog/review', 'sort=r.date_added' . $url);
		
		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $review_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function getForm() {
		$this->template->load('catalog/review_form');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
 		
		if (isset($this->error['product'])) {
			$this->data['error_product'] = $this->error['product'];
		} else {
			$this->data['error_product'] = '';
		}
		
 		if (isset($this->error['author'])) {
			$this->data['error_author'] = $this->error['author'];
		} else {
			$this->data['error_author'] = '';
		}
		
 		if (isset($this->error['text'])) {
			$this->data['error_text'] = $this->error['text'];
		} else {
			$this->data['error_text'] = '';
		}
		
 		if (isset($this->error['rating'])) {
			$this->data['error_rating'] = $this->error['rating'];
		} else {
			$this->data['error_rating'] = '';
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
				
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('catalog/review', $url));

		if (!isset($_GET['review_id'])) {
			$this->data['action'] = $this->url->link('catalog/review/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('catalog/review/update', 'review_id=' . $_GET['review_id'] . $url);
		}
		
		$this->data['cancel'] = $this->url->link('catalog/review', $url);

		if (isset($_GET['review_id']) && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$review_info = $this->model_catalog_review->getReview($_GET['review_id']);
		}
			
		if (isset($_POST['product_id'])) {
			$this->data['product_id'] = $_POST['product_id'];
		} elseif (!empty($review_info)) {
			$this->data['product_id'] = $review_info['product_id'];
		} else {
			$this->data['product_id'] = '';
		}

		if (isset($_POST['product'])) {
			$this->data['product'] = $_POST['product'];
		} elseif (!empty($review_info)) {
			$this->data['product'] = $review_info['product'];
		} else {
			$this->data['product'] = '';
		}
				
		if (isset($_POST['author'])) {
			$this->data['author'] = $_POST['author'];
		} elseif (!empty($review_info)) {
			$this->data['author'] = $review_info['author'];
		} else {
			$this->data['author'] = '';
		}

		if (isset($_POST['text'])) {
			$this->data['text'] = $_POST['text'];
		} elseif (!empty($review_info)) {
			$this->data['text'] = $review_info['text'];
		} else {
			$this->data['text'] = '';
		}

		if (isset($_POST['rating'])) {
			$this->data['rating'] = $_POST['rating'];
		} elseif (!empty($review_info)) {
			$this->data['rating'] = $review_info['rating'];
		} else {
			$this->data['rating'] = '';
		}

		if (isset($_POST['status'])) {
			$this->data['status'] = $_POST['status'];
		} elseif (!empty($review_info)) {
			$this->data['status'] = $review_info['status'];
		} else {
			$this->data['status'] = '';
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/review')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$_POST['product_id']) {
			$this->error['product'] = $this->_('error_product');
		}
		
		if ((strlen($_POST['author']) < 3) || (strlen($_POST['author']) > 64)) {
			$this->error['author'] = $this->_('error_author');
		}

		if (strlen($_POST['text']) < 1) {
			$this->error['text'] = $this->_('error_text');
		}
				
		if (!isset($_POST['rating'])) {
			$this->error['rating'] = $this->_('error_rating');
		}
		
		return $this->error ? false : true;
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/review')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
	}
}