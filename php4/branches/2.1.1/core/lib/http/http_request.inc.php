<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: http_request.inc.php 367 2004-01-30 14:38:37Z server $
*
***********************************************************************************/ 
				
if(ini_get('magic_quotes_gpc'))
	$_REQUEST = _strip_http_slashes($_REQUEST);

function _strip_http_slashes($data, $result=array())
{		
	foreach($data as $k => $v)
  	if(is_array($v))
  		$result[$k] = _strip_http_slashes($v);
		else
			$result[$k] = stripslashes($v);
			
	return $result;
}
	
?>