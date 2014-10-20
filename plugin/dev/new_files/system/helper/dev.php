<?php
//Amplo Performance Logging
define("SHOW_DB_PROFILE", (defined("DB_PROFILE") & DB_PROFILE) ? true : option('config_performance_log', false));

if (!defined("DB_PROFILE_NO_CACHE")) {
define("DB_PROFILE_NO_CACHE", true);
}
