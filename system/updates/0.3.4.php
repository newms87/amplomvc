<?php

if ($this->db->hasColumn('page', 'options')) {
	$pages = $this->Model_Page->getRecords();

	foreach ($pages as $page) {
		if (!empty($page['options'])) {
			$options = json_decode($page['options'], true);

			if ($options && is_string($options)) {
				$options = json_decode($options, true);
			}

			if (is_array($options)) {
				$this->Model_Page->save($page['page_id'], array(
					'meta'    => $options,
					'options' => '',
				));
			}
		}
	}

	$this->db->dropColumn('page', 'options');
}
