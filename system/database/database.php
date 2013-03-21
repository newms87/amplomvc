<?php
interface Database{
   
   public function get_error();
      
   public function query($sql);
      
   public function escape($value);
   
   public function escape_html($value);
   
   public function countAffected();

   public function getLastId();
   
   public function __destruct();
}
