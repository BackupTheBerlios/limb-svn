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
require_once(LIMB_DIR . 'class/lib/error/debug.class.php');
require_once(LIMB_DIR . 'class/core/user.class.php');
require_once(LIMB_DIR . 'class/lib/system/sys.class.php');

if(!defined('ERROR_HANDLER_TYPE'))
	debug :: set_handle_type('custom');
else
	debug :: set_handle_type(ERROR_HANDLER_TYPE);

function error($description, $error_place='', $params=array()) 
{
	if(isset($GLOBALS['error_recursion']) && $GLOBALS['error_recursion'])
		die('error recursion');

	if(sys :: exec_mode() != 'cli' && $_SERVER['SERVER_PORT'] == 81)
		trigger_error('error', E_USER_WARNING);		
		
	$GLOBALS['error_recursion'] = true;
	
	$description = $description . "\n\nback trace:\n" . get_trace_back();
		
	rollback_user_transaction();

	debug :: write_error($description, $error_place, $params);
	
	if(sys :: exec_mode() == 'cli')
	  echo debug :: parse_cli_console();
	elseif (debug :: is_console_enabled())
		echo debug :: parse_html_console();
		
	ob_end_flush();
			
	exit;
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
					$trace_string .= htmlspecialchars(substr((string) @$arg, 0, 32));
					
					if (strlen($arg) > 32) 
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