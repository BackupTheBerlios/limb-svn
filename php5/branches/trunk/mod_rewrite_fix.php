<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/	
require_once(LIMB_DIR . '/class/lib/util/complex_array.class.php');

$url = parse_url($_SERVER['REQUEST_URI']);

$_SERVER['QUERY_STRING'] = $url['query'];
$_SERVER['PHP_SELF'] = $url['path'];

if($url['query'])
{
	parse_str($url['query'], $output);
	
	$_GET = complex_array :: array_merge($_GET, $output);
}

?>