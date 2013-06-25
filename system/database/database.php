<?php
interface Database{
	
	public function getError();
		
	public function query($sql);
		
	public function escape($value);
	
	public function escapeHtml($value);
	
	public function countAffected();

	public function getLastId();
	
	public function __destruct();
}
