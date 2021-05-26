<?php
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'admin');
define('DB_USER_PASSWORD', 'password');
define('DB_NAME', 'switch_db');
define('DRIVE_NAME','change_me_please');
define('SECRET_KEY_FOR_MIGRATION','change_me_please');
define('DOMAIN_NAME','http://domain.com');
define('ROUGH_SPACE_LIMIT', 10000); # in Megabytes. For now this is just a rough estimate. if the limit is 20gb and the current cache is 19gb big and the requested file is bigger than 1 gb it will still be added to the cache.
define('FILE_CACHE_PATH','/home/user/temp/'); # make sure this directory exists
define('CONFIG_FILE_PATH_RCLONE','/home/user/.config/rclone/rclone.conf');
define('SUCCESS_MESSAGE','THIS IS WORKING');