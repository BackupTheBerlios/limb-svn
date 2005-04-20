<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/debug/debug.class.php');
require_once(LIMB_DIR . '/core/lib/security/user.class.php');
require_once(LIMB_DIR . '/core/lib/system/sys.class.php');
require_once(LIMB_DIR . '/core/lib/i18n/utf8.inc.php');

if(!defined('ERROR_HANDLER_TYPE'))
  debug :: set_handle_type('custom');
else
  debug :: set_handle_type(ERROR_HANDLER_TYPE);

function error($description, $error_place='', $params=array())
{
  trigger_error('error', E_USER_WARNING);

  if(isset($GLOBALS['error_recursion']) && $GLOBALS['error_recursion'])
    die($description . ' - (error recursion!!!)');

  $description = $description . "\n\nback trace:\n" . get_trace_back();

  $GLOBALS['error_recursion'] = true;

  rollback_user_transaction();

  debug :: write_error($description, $error_place, $params);

  if (debug :: is_console_enabled())
  {
    debug :: write_error($description, $error_place, $params);
    echo debug :: parse_console();
  }

  ob_end_flush();

  exit();
}

function get_trace_back()
{
  // based on PHP manual page for debug_backtrace()

  $trace_string = '';

  if (!version_compare(PHPVERSION(), '4.3', '>='))
    return $trace_string;

  $trace = debug_backtrace();

  foreach ($trace as $line)
  {
    if (in_array($line['function'], array(
        'raiseerror', 'trigger_error', 'errorhandlerdispatch',
        'handleerror', 'handleframeworkerror', 'displaytraceback' )))
        continue;

    $trace_string .= '* ';
    if (isset($line['class']))
    {
      $trace_string .= $line['class'];
      $trace_string .= ".";
    }
    $trace_string .= $line['function'];
    $trace_string .= "(";
    if (isset($line['args']))
    {
      $sep = '';
      foreach ($line['args'] as $arg)
      {
        $trace_string .= $sep;
        $sep = ', ';

        if (is_null($arg))
          $trace_string .= 'NULL';
        elseif (is_array($arg))
          $trace_string .= 'ARRAY[' . sizeof($arg) . ']';
        elseif (is_object($arg))
          $trace_string .= 'OBJECT:' . get_class($arg);
        elseif (is_bool($arg))
          $trace_string .= $arg ? 'TRUE' : 'FALSE';
        else
        {
          $trace_string .= '"';
          $trace_string .= htmlspecialchars(utf8_substr((string) @$arg, 0, 32));

          if (utf8_strlen($arg) > 32)
            $trace_string .= '...';

          $trace_string .= '"';
        }
      }
    }
    $trace_string .= ")";

    if (isset($line['file']))
    {
      $trace_string .= $line['file'];
      $trace_string .= " line ";
      $trace_string .= $line['line'];
    }
    $trace_string .= "\n\n";
  }

  return $trace_string;
}
?>