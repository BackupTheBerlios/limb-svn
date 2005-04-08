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

//restore_error_handler() won't cut it at times,
//since it restores the last error handler set
function restore_native_error_handler()
{
  do
  {
    $handler = set_error_handler('dummyErrorHandler');
    restore_error_handler();
    restore_error_handler();
  }
  while($handler !== null);
}

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