<?php

$this->db->dropColumn('plugin_registry', 'plugin_file_modified');
$this->db->dropColumn('plugin_registry', 'live_file_modified');