<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 

require_once(LIMB_DIR . 'core/model/stats/stats_register.class.php');
require_once(LIMB_DIR . 'core/model/response/response.class.php');

$stats_register = new stats_register();
$response = new response();
$stats_register->register(-1, '', $response->get_status());

header("Location: /root");
exit;
?> 