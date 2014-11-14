<?php
//TODO: Change file to database_inteface and class to DatabaseInteface (update for db implementations)
interface DatabaseInterface
{
	public function getError();

	public function query($sql);

	public function escape($value);

	public function escapeHtml($value);

	public function countAffected();

	public function getLastId();
}
