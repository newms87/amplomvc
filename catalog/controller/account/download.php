<?php
class ControllerAccountDownload extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/download');

			$this->url->redirect($this->url->link('account/login'));
		}
					
		$this->language->load('account/download');

		$this->document->setTitle($this->_('heading_title'));

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
			$this->breadcrumb->add($this->_('text_downloads'), $this->url->link('account/download'));

		$download_total = $this->model_account_download->getTotalDownloads();
		
		if ($download_total) {
		$this->template->load('account/download');

			if (isset($_GET['page'])) {
				$page = $_GET['page'];
			} else {
				$page = 1;
			}
	
			$this->data['downloads'] = array();
			
			$results = $this->model_account_download->getDownloads(($page - 1) * $this->config->get('config_catalog_limit'), $this->config->get('config_catalog_limit'));
			
			foreach ($results as $result) {
				if (file_exists(DIR_DOWNLOAD . $result['filename'])) {
					$size = filesize(DIR_DOWNLOAD . $result['filename']);

					$i = 0;

					$suffix = array(
						'B',
						'KB',
						'MB',
						'GB',
						'TB',
						'PB',
						'EB',
						'ZB',
						'YB'
					);

					while (($size / 1024) > 1) {
						$size = $size / 1024;
						$i++;
					}

					$this->data['downloads'][] = array(
						'order_id'	=> $result['order_id'],
						'date_added' => $this->tool->format_datetime($result['date_added'], $this->language->getInfo('date_format_short')),
						'name'		=> $result['name'],
						'remaining'  => $result['remaining'],
						'size'		=> round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i],
						'href'		=> $this->url->link('account/download/download', 'order_download_id=' . $result['order_download_id'])
					);
				}
			}
		
			$this->pagination->init();
			$this->pagination->total = $download_total;
			$this->data['pagination'] = $this->pagination->render();
			
			$this->data['continue'] = $this->url->link('account/account');

			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);
							
			$this->response->setOutput($this->render());
		} else {
		$this->template->load('error/not_found');

			$this->language->set('text_error', $this->_('text_empty'));

			$this->data['continue'] = $this->url->link('account/account');

			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);
										
			$this->response->setOutput($this->render());
		}
	}

	public function download() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/download');

			$this->url->redirect($this->url->link('account/login'));
		}

		if (isset($_GET['order_download_id'])) {
			$order_download_id = $_GET['order_download_id'];
		} else {
			$order_download_id = 0;
		}
		
		$download_info = $this->model_account_download->getDownload($order_download_id);
		
		if ($download_info) {
			$file = DIR_DOWNLOAD . $download_info['filename'];
			$mask = basename($download_info['mask']);

			if (!headers_sent()) {
				if (file_exists($file)) {
					header('Content-Type: application/octet-stream');
					header('Content-Description: File Transfer');
					header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));
					
					readfile($file, 'rb');
					
					$this->model_account_download->updateRemaining($_GET['order_download_id']);
					
					exit;
				} else {
					exit('Error: Could not find file ' . $file . '!');
				}
			} else {
				exit('Error: Headers already sent out!');
			}
		} else {
			$this->url->redirect($this->url->link('account/download'));
		}
	}
}