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
require_once(LIMB_DIR . '/core/lib/util/complex_array.class.php');

$url = parse_url($_SERVER['REQUEST_URI']);

$_SERVER['QUERY_STRING'] = $url['query'];

if($url['query'])
{
	parse_str($url['query'], $output);
	
	$_GET = complex_array :: array_merge($_GET, $output);
	$_REQUEST = complex_array :: array_merge($_REQUEST, $output);
}

?>