<?php
interface SetupPlugin {
   public function install($registry, &$controller_adapters, &$db_requests, &$language_extensions, &$file_modifications);
   
   public function uninstall($registry);
   
   public function update($version, $registry);
}