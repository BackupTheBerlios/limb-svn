<?php
	if (file_exists('protect.php'))
		include_once('protect.php');
	
	require_once('setup.php');
	
	require(LIMB_DIR.'/mod_rewrite_fix.php');
	require(LIMB_DIR . '/root.php');
?> 