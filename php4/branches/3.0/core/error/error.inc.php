<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/error/Debug.class.php');

if(!defined('ERROR_HANDLER_TYPE'))
  Debug :: setHandleType('custom');
else
  Debug :: setHandleType(ERROR_HANDLER_TYPE);

function isErrorHandlerInstalled($handler)
{
  $prev_handler = set_error_handler('dummyErrorHandler');
  $res = $prev_handler === $handler;
  set_error_handler($prev_handler);

  return $res;
}

function isPHPErrorHandlerInstalled()
{
  $prev_handler = set_error_handler('dummyErrorHandler');
  $res = $prev_handler === NULL;
  set_error_handler($prev_handler);

  return $res;
}

function dummyErrorHandler(){}

?>