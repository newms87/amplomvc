<?php

class App_Controller_Account_Download extends Controller
{
	public function index()
	{
		if (!is_logged()) {
			$this->session->set('redirect', site_url('account/download'));

			redirect('customer/login');
		}

		set_page_info('title', _l("Account Downloads"));

		breadcrumb(_l("Home"), site_url());
		breadcrumb(_l("Account"), site_url('account'));
		breadcrumb(_l("Downloads"), site_url('account/download'));

		$download_total = $this->Model_Account_Download->getTotalDownloads();

		if ($download_total) {
			if (isset($_GET['page'])) {
				$page = $_GET['page'];
			} else {
				$page = 1;
			}

			$data['downloads'] = array();

			$results = $this->Model_Account_Download->getDownloads(($page - 1) * option('site_list_limit'), option('site_list_limit'));

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
						'href'       => site_url('account/download/download', 'order_download_id=' . $result['order_download_id'])
					);
				}
			}

			$data['continue'] = site_url('account');

			$data['total'] = $download_total;

			output($this->render('account/download', $data));
		} else {
			message('error', _l("You have not made any previous downloadable orders!"));

			$data['continue'] = site_url('account');

			output($this->render('error/not_found', $data));
		}
	}

	public function download()
	{
		if (!is_logged()) {
			$this->session->set('redirect', site_url('account/download'));

			redirect('customer/login');
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
			redirect('account/download');
		}
	}
}
