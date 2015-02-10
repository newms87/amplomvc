<?php

class Tag Extends Library
{
	public function get($text)
	{
		return $this->queryVar("SELECT tag_id FROM {$this->t['tag']} WHERE LCASE(text) = '" . $this->escape(strtolower(trim($text))) . "'");
	}

	public function add($text)
	{
		if (!$text) {
			return 0;
		}

		$tag_id = $this->get($text);

		if (!$tag_id) {
			$tag = array(
				'text' => strtolower(trim($text)),
			);

			$tag_id = $this->insert('tag', $tag);
		}

		return $tag_id;
	}

	public function addAll($tags)
	{
		if (is_string($tags)) {
			$tags = explode(',', $tags);
		}

		$tag_ids = array();

		foreach ($tags as $tag) {
			$tag_ids[$this->add($tag)] = true;
		}

		unset($tag_ids[0]);

		return array_keys($tag_ids);
	}
}
