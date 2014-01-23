<?php
//TODO: Change file to database_inteface and calss to DatabaseInteface (update for db implementations)
interface Database
{
	public function getError();

	public function query($sql);

	public function escape($value);

	public function escapeHtml($value);

	public function countAffected();

	public function getLastId();

	public function __destruct();
}
