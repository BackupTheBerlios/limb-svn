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
require_once(LIMB_DIR . 'core/lib/debug/debug.class.php');
require_once(LIMB_DIR . 'core/lib/security/user.class.php');
require_once(LIMB_DIR . 'core/lib/system/sys.class.php');

if(!defined('ERROR_HANDLER_TYPE'))
	debug :: set_handle_type('custom');
else
	debug :: set_handle_type(ERROR_HANDLER_TYPE);

function error($description, $error_place='', $params=array()) 
{
	if(isset($GLOBALS['error_recursion']) && $GLOBALS['error_recursion'])
		die();
		
	$GLOBALS['error_recursion'] = true;
	
	if(defined('DEVELOPER_ENVIROMENT'))
	{
		trigger_error('error', E_USER_WARNING);
		
		echo(  $description . '<br>' . $error_place . '<br><pre>');
		print_r($params);
		echo('</pre>');
	}
	
	$description = $description . "\n\nback trace:\n" . get_trace_back();
		
	rollback_user_transaction();

	debug :: set_message_output(DEBUG_OUTPUT_MESSAGE_STORE | DEBUG_OUTPUT_MESSAGE_SEND);
	debug :: write_error($description, $error_place, $params);
	
	if (debug :: is_console_enabled())
	{
		debug :: write_error($description, $error_place, $params);
		echo debug :: parse_console();
	}
		
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