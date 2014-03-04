<?php

class Tree
{
	private $nodes, $head;

	public function __construct()
	{
		$this->nodes = array();
		$this->head  = new Node();
	}

	public function addNodes($nodes, $id = null, $name = null)
	{
		foreach ($nodes as $node) {
			if ($id) {
				$node['id'] = $node[$id];
			}

			if ($name) {
				$node['name'] = $node[$name];
			}

			$this->addNode($node);
		}
	}

	public function addNode($node)
	{
		$node = new Node($node);

		if (empty($node->parent_id)) {
			$this->head->children[] = $node;
			$node->parent           = $this->head;
		} else {
			foreach ($this->nodes as $n) {
				if ($n->id == $node->parent_id) {
					$n->children[] = $node;
					$node->parent  = $n;
				}
			}
		}

		$this->nodes[] = $node;
	}

	public function printTree($pointer = null)
	{
		if (!$pointer) {
			$pointer = $this->head;
		}

		echo "<div class=\"parent\">$pointer->name</div>";
		echo "<div class=\"children\">";
		if (!empty($pointer->children)) {
			foreach ($pointer->children as $child) {
				$this->printTree($child);
			}
		}
		echo "</div>";
	}
}

class Node
{
	public $parent, $children;

	public function __construct($data = null)
	{
		if (!empty($data)) {
			foreach ($data as $key => $value) {
				$this->$key = $value;
			}
		} else {
			$this->id   = 0;
			$this->name = 'HEAD';
		}
	}
}
