<?php
class Catalog_Controller_Account_Download extends Controller
{
	public function index()
	{
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', $this->url->link('account/download'));

			$this->url->redirect('customer/login');
		}

		$this->document->setTitle(_l("Account Downloads"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Downloads"), $this->url->link('account/download'));

		$download_total = $this->Model_Account_Download->getTotalDownloads();

		if ($download_total) {
			if (isset($_GET['page'])) {
				$page = $_GET['page'];
			} else {
				$page = 1;
			}

			$data['downloads'] = array();

			$results = $this->Model_Account_Download->getDownloads(($page - 1) * $this->config->get('config_catalog_limit'), $this->config->get('config_catalog_limit'));

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

					$data['downloads'][] = array(
						'order_id'   => $result['order_id'],
						'date_added' => $this->date->format($result['date_added'], 'short'),
						'name'       => $result['name'],
						'remaining'  => $result['remaining'],
						'size'       => round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i],
						'href'       => $this->url->link('account/download/download', 'order_download_id=' . $result['order_download_id'])
					);
				}
			}

			$this->pagination->init();
			$this->pagination->total  = $download_total;
			$data['pagination'] = $this->pagination->render();

			$data['continue'] = $this->url->link('account/account');

			$this->response->setOutput($this->render('account/download', $data));
		} else {
			$this->message->add('error', _l("You have not made any previous downloadable orders!"));

			$data['continue'] = $this->url->link('account/account');

			$this->response->setOutput($this->render('error/not_found', $data));
		}
	}

	public function download()
	{
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', $this->url->link('account/download'));

			$this->url->redirect('customer/login');
		}

		if (isset($_GET['order_download_id'])) {
			$order_download_id = $_GET['order_download_id'];
		} else {
			$order_download_id = 0;
		}

		$download_info = $this->Model_Account_Download->getDownload($order_download_id);

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

					$this->Model_Account_Download->updateRemaining($_GET['order_download_id']);

					exit;
				} else {
					exit('Error: Could not find file ' . $file . '!');
				}
			} else {
				exit('Error: Headers already sent out!');
			}
		} else {
			$this->url->redirect('account/download');
		}
	}
}
