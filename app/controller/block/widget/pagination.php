<?php

/**
 * Class App_Controller_Block_Widget_Pagination
 * Name: List Pagination
 */
class App_Controller_Block_Widget_Pagination extends App_Controller_Block_Block
{
	public function build($settings)
	{
		$settings += array(
			'total'         => 0,
			'template'      => 'block/widget/pagination',
			'page'          => isset($_GET['page']) ? (int)$_GET['page'] : 1,
			'limit'         => isset($_GET['limit']) ? (int)$_GET['limit'] : option('admin_list_limit'),
			'url'           => '',
			'num_links'     => 10,
			'text'          => _l("Showing %start% to %end% of %total% (%pages% Pages)"),
			'text_position' => 'after',
			'class'         => 'default',
		);

		if ($settings['total'] < 1) {
			return '';
		}

		if (empty($settings['path'])) {
			$settings['path'] = $this->route->getPath();
		}

		$url_query = $_GET;
		unset($url_query['page']);

		if ($settings['page'] < 1) {
			$settings['page'] = 1;
		}

		if ($settings['limit'] < 1) {
			$settings['limit'] = $settings['total'];
			$query['limit']    = 0;
		} else {
			$query['limit'] = $settings['limit'];
		}

		$num_pages = ceil($settings['total'] / $settings['limit']);

		if ($num_pages > $settings['num_links']) {
			$num_before = floor(($settings['num_links'] - 1) / 2);
			$num_after  = floor($settings['num_links'] / 2);

			if ($settings['page'] + $num_after >= $num_pages) {
				$start = $num_pages - $settings['num_links'];
				$end   = $num_pages;
			} elseif ($settings['page'] - $num_before <= 1) {
				$start = 1;
				$end   = $settings['num_links'];
			} else {
				$start = $settings['page'] - $num_before;
				$end   = $settings['page'] + $num_after;
			}
		} else {
			$start = 1;
			$end   = $num_pages;
		}

		//Link Url (without page, as this is variable)
		$url = site_url($settings['path'], $url_query);

		//Pages
		$pages = array();

		if ($num_pages > 1) {
			for ($i = $start; $i <= $end; $i++) {
				$pages[$i] = $url . '&page=' . $i;
			}
		}

		//Pagination Text
		$item_start = (($settings['page'] - 1) * $settings['limit']) + 1;
		$item_end   = ($settings['page'] * $settings['limit'] > $settings['total']) ? $settings['total'] : $settings['page'] * $settings['limit'];

		$insertables = array(
			'start' => $item_start,
			'end'   => $item_end,
			'total' => $settings['total'],
			'pages' => $num_pages,
		);

		$settings['text'] = insertables($insertables, $settings['text']);

		//Template Data
		$settings += array(
			'url_first' => $url . '&page=1',
			'url_prev'  => $url . '&page=' . ($settings['page'] - 1),
			'url_next'  => $url . '&page=' . ($settings['page'] + 1),
			'url_last'  => $url . '&page=' . $num_pages,
			'start'     => $start,
			'end'       => $end,
			'num_pages' => $num_pages,
			'pages'     => $pages,
		);

		$this->render($settings['template'], $settings);
	}
}
