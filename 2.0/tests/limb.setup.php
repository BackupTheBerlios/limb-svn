<?php
define('LIMB_DIR', 'c:/var/dev/limb/2.0/');
define('PROJECT_DIR', 'c:/var/dev/limb/2.0/tests/');

define('ERROR_HANDLER_TYPE', 0);
define('DEBUG_CONSOLE_ENABLED', true);

define('DEFAULT_MANAGEMENT_LOCALE_ID', 'en');
define('DEFAULT_CONTENT_LOCALE_ID','en');

define('SESSION_USE_DB', false);

define('CONTENT_LOCALE_ID', 'en');
define('MANAGEMENT_LOCALE_ID', 'en');
    
define('DB_TYPE','mysql');
define('DB_HOST','192.168.0.6');
define('DB_LOGIN','root');
define('DB_PASSWORD','test');
define('DB_NAME','limb2_tests');

define('DB_AUTO_CONSTRAINTS', false);

define('MEDIA_DIR', PROJECT_DIR . '/tmp/');
define('CACHE_DIR', PROJECT_DIR . '/var/cache/');
define('VAR_URL', '/tests/var/');

require_once(LIMB_DIR . 'setup.php');

?>