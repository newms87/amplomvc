<?php
class Request
{
  	public function __construct()
  	{
		array_walk_recursive($_GET, array($this, 'clean'));
		array_walk_recursive($_POST, array($this, 'clean'));
		array_walk_recursive($_REQUEST, array($this, 'clean'));
		array_walk_recursive($_COOKIE, array($this, 'clean'));
		array_walk_recursive($_SERVER, array($this, 'clean'));
	}
	
	public function isPost()
	{
		return $_SERVER['REQUEST_METHOD'] === 'POST';
	}
	
	public function isGet()
	{
		return $_SERVER['REQUEST_METHOD'] === 'GET';
	}
	
  	public function clean(&$value)
  	{
		$value = htmlspecialchars(stripslashes($value), ENT_COMPAT);
	}
}