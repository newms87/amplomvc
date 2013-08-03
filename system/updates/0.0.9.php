<?php
$this->db->changeColumn('url_alias', 'keyword', 'alias', "VARCHAR(255) NOT NULL  AFTER `url_alias_id`");
$this->db->changeColumn('url_alias', 'route', 'path', "VARCHAR(255) NOT NULL");
$this->db->query("UPDATE " . DB_PREFIX . "url_alias SET store_id = 0 WHERE store_id = '-1'");

//Update .htaccess Files
$htaccess = SITE_DIR . '.htaccess';
file_put_contents($htaccess, str_replace('_route_', '_path_', file_get_contents($htaccess)));