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
define('DEBUG_LEVEL_NOTICE', 1);
define('DEBUG_LEVEL_WARNING', 2);
define('DEBUG_LEVEL_ERROR', 3);
define('DEBUG_TIMING_POINT', 4);
define('DEBUG_LEVEL', 5);

define('DEBUG_SHOW_NOTICE', 1 << (DEBUG_LEVEL_NOTICE - 1));
define('DEBUG_SHOW_WARNING', 1 << (DEBUG_LEVEL_WARNING - 1));
define('DEBUG_SHOW_ERROR', 1 << (DEBUG_LEVEL_ERROR - 1));
define('DEBUG_SHOW_TIMING_POINT', 1 << (DEBUG_TIMING_POINT - 1));
define('DEBUG_SHOW', 1 << (DEBUG_LEVEL - 1));
define('DEBUG_SHOW_ALL', DEBUG_SHOW_NOTICE | DEBUG_SHOW_WARNING | DEBUG_SHOW_ERROR | DEBUG_SHOW_TIMING_POINT | DEBUG_SHOW);

define('DEBUG_HANDLE_NATIVE', 0);
define('DEBUG_HANDLE_CUSTOM', 1);
define('DEBUG_HANDLE_TRIGGER_ERROR', 2);

define('DEBUG_OUTPUT_MESSAGE_SCREEN', 1);
define('DEBUG_OUTPUT_MESSAGE_STORE', 2);
define('DEBUG_OUTPUT_MESSAGE_SEND', 4);

require_once(LIMB_DIR . 'core/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . 'core/lib/system/dir.class.php');
require_once(LIMB_DIR . 'core/lib/system/sys.class.php');
require_once(LIMB_DIR . 'core/lib/util/log.class.php');
require_once(LIMB_DIR . 'core/lib/mail/send_plain_mail.inc.php');

class debug
{ 
	// String array containing the debug information
	var $debug_strings = array(); 
	
	// Array which contains the time points
	var $time_points = array(); 
	
	// Array wich contains time accumulators
	var $time_accumulator_list = array(); 
	
	// Determines which debug messages should be shown
	var $show_types; 
	
	// Determines what to do with php errors, ignore, fetch or output
	var $handle_type = DEBUG_HANDLE_NATIVE; 
	
	// An array of the output_formats for the different debug levels
	var $output_format; 
	
	// An array of log_files used by the debug class with each key being the debug level
	var $log_files; 
	
	// How many places behing . should be displayed when showing times
	var $timing_accuracy = 4; 
	
	// How many places behing . should be displayed when showing percentages
	var $percent_accuracy = 4; 
	
	// Determines how messages are output (screen/log/mail)
	var $message_output = DEBUG_OUTPUT_MESSAGE_STORE; 
	
	// A list of message types
	var $message_types; 
	
	// A map with message types and whether they should do file logging.
	var $log_file_enabled; 
	
	// The time when the script was started
	var $script_start;

	function debug()
	{
		$this->output_format = array(
			DEBUG_LEVEL_NOTICE => array('color' => 'green',
				'name' => 'Notice'),
			DEBUG_LEVEL_WARNING => array('color' => 'orange',
				'name' => 'Warning'),
			DEBUG_LEVEL_ERROR => array('color' => 'red',
				'name' => 'Error'),
			DEBUG_LEVEL => array('color' => 'brown',
				'name' => 'Debug'),
			DEBUG_TIMING_POINT => array('color' => 'blue',
				'name' => 'Timing'));

		$this->log_files = array(
			DEBUG_LEVEL_NOTICE => array(VAR_DIR . 'log/',
				'notice.log'),
			DEBUG_LEVEL_WARNING => array(VAR_DIR . 'log/',
				'warning.log'),
			DEBUG_LEVEL_ERROR => array(VAR_DIR . 'log/',
				'error.log'),
			DEBUG_TIMING_POINT => array(VAR_DIR . 'log/',
				'time.log'),
			DEBUG_LEVEL => array(VAR_DIR . 'log/',
				'debug.log'));

		$this->message_types = array(
			DEBUG_LEVEL_NOTICE,
			DEBUG_LEVEL_WARNING,
			DEBUG_LEVEL_ERROR,
			DEBUG_TIMING_POINT,
			DEBUG_LEVEL);

		$this->log_file_enabled = array(
			DEBUG_LEVEL_NOTICE => true,
			DEBUG_LEVEL_WARNING => true,
			DEBUG_LEVEL_ERROR => true,
			DEBUG_TIMING_POINT => false,
			DEBUG_LEVEL => true);

		$this->show_types = DEBUG_SHOW_ALL;
		$this->handle_type = DEBUG_HANDLE_NATIVE;
		$this->old_handler = false;
		$this->script_start = debug :: _time_to_float(microtime());
		$this->time_accumulator_list = array();
		$this->time_accumulator_group_list = array();
	} 

	function reset()
	{
		$this->debug_strings = array();
		$this->time_accumulator_list = array();
		$this->time_accumulator_group_list = array();
	} 

	function &instance()
	{
		if(class_exists('debug_mock'))
			$impl =& instantiate_object('debug_mock');
		else
			$impl =& instantiate_object('debug');

		return $impl;
	} 

	/*
   Returns true if the message type $type can be shown.
  */
	function show_message($type)
	{
		$debug = &debug::instance();
		return $debug->show_types & $type;
	} 

	/*
   Determines how PHP errors are handled. If $type is DEBUG_HANDLE_TRIGGER_ERROR all error messages
   is sent to PHP using trigger_error(), if $type is DEBUG_HANDLE_CUSTOM all error messages
   from PHP is fetched using a custom error handler and output as a usual debug message.
   If $type is DEBUG_HANDLE_NATIVE there is no error exchange between PHP and debug.
  */
	function set_handle_type($type)
	{
		if (!isset($this) || get_class($this) != 'debug')
			$debug =& debug::instance();
		else
			$debug =& $this;

		if ($type != DEBUG_HANDLE_TRIGGER_ERROR && $type != DEBUG_HANDLE_CUSTOM)
			$type = DEBUG_HANDLE_NATIVE;

		if ($type == $debug->handle_type)
			return;

		if ($debug->handle_type == DEBUG_HANDLE_CUSTOM)
			restore_error_handler();

		switch ($type)
		{
			case DEBUG_HANDLE_CUSTOM:
				set_error_handler('debug_error_handler');
			break;

			case DEBUG_HANDLE_TRIGGER_ERROR:
				restore_error_handler();
			break;

			case DEBUG_HANDLE_NATIVE:
		} 
		$debug->handle_type = $type;
	} 

	/*
   Sets types to be shown to $types and returns the old show types.
   If $types is not supplied the current value is returned and no change is done.
   $types is one or more of DEBUG_SHOW_NOTICE, DEBUG_SHOW_WARNING, DEBUG_SHOW_ERROR, DEBUG_SHOW_TIMING_POINT
   or'ed together.
  */
	function show_types($types = false)
	{
		if (!isset($this) || get_class($this) != 'debug')
			$debug =& debug::instance();
		else
			$debug =& $this;

		if ($types === false)
			return $debug->show_types;

		$old_types = $debug->show_types;
		$debug->show_types = $types;

		return $old_types;
	} 

	/*
   Handles PHP errors, creates notice, warning and error messages for
   the various PHP error types.
  */
	function error_handler($errno, $errstr, $errfile, $errline)
	{
		if (error_reporting() == 0) // error-control operator is used
			return;

		if (!debug::is_debug_enabled())
			return;

		$str = "$errstr in $errfile on line $errline";
		$errnames = &$GLOBALS['DEBUG_PHP_ERROR_NAMES'];
		if (!is_array($errnames))
		{
			$errnames = array(E_ERROR => 'E_ERROR',
				E_PARSE => 'E_PARSE',
				E_CORE_ERROR => 'E_CORE_ERROR',
				E_COMPILE_ERROR => 'E_COMPILE_ERROR',
				E_USER_ERROR => 'E_USER_ERROR',
				E_WARNING => 'E_WARNING',
				E_CORE_WARNING => 'E_CORE_WARNING',
				E_COMPILE_WARNING => 'E_COMPILE_WARNING',
				E_USER_WARNING => 'E_USER_WARNING',
				E_NOTICE => 'E_NOTICE',
				E_USER_NOTICE => 'E_USER_NOTICE');
		} 

		$errname = 'unknown';
		if (isset($errnames[$errno]))
			$errname = $errnames[$errno];

		switch ($errno)
		{
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
				{
					debug::write_error($str, 'PHP');
				} 
				break;

			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
				{
					debug::write_warning($str, 'PHP');
				} 
				break;

			case E_USER_NOTICE:
			case E_NOTICE:
				{
					debug::write_notice($str, 'PHP');
				} 
				break;
		} 
	} 

	/*
    Writes a debug notice.
  */
	function write_notice($string, $code_line = '', $params = array())
	{
		if (!debug::is_debug_enabled())
			return;
		if (!debug::show_message(DEBUG_SHOW_NOTICE))
			return;

		$debug =& debug::instance();
		if ($debug->handle_type == DEBUG_HANDLE_TRIGGER_ERROR)
			trigger_error($string, E_USER_NOTICE);
		else
			$debug->write(DEBUG_LEVEL_NOTICE, $string, $code_line, $params);
	} 

	/*
    writes a debug warning.
  */
	function write_warning($string, $code_line = '', $params = array())
	{
		if (!debug::is_debug_enabled())
			return;
		if (!debug::show_message(DEBUG_SHOW_WARNING))
			return;

		$debug =& debug::instance();
		if ($debug->handle_type == DEBUG_HANDLE_TRIGGER_ERROR)
			trigger_error($string, E_USER_WARNING);
		else
			$debug->write(DEBUG_LEVEL_WARNING, $string, $code_line, $params);
	} 

	/*
    Writes a debug error.
  */
	function write_error($string, $code_line = '', $params = array())
	{
		if (!debug::is_debug_enabled())
			return;
		if (!debug::show_message(DEBUG_SHOW_ERROR))
			return;

		$debug =& debug::instance();
		if ($debug->handle_type == DEBUG_HANDLE_TRIGGER_ERROR)
			trigger_error($string, E_USER_ERROR);
		else
			$debug->write(DEBUG_LEVEL_ERROR, $string, $code_line, $params);
	} 
	
	/*
    Writes a debug message.
  */
	function write_debug($string, $code_line = '', $params = array())
	{
		if (!debug::is_debug_enabled())
			return;
		if (!debug::show_message(DEBUG_SHOW_ERROR))
			return;

		$debug =& debug::instance();
		if ($debug->handle_type == DEBUG_HANDLE_TRIGGER_ERROR)
			trigger_error($string, E_USER_NOTICE);
		else
			$debug->write(DEBUG_LEVEL, $string, $code_line, $params);
	} 
	
	function _send_mail($description, $verbosity_level)
	{
		
		$title = '';
		$headers = array();
		
		switch ($verbosity_level)
		{
			case DEBUG_LEVEL_NOTICE:
				$title .= ' debug notice';
				$headers['X-Priority'] = '0 (Low)';
			break;
			
			case DEBUG_LEVEL_WARNING:
				$title .= ' debug warning';
			break;
			
			case DEBUG_LEVEL_ERROR:
				$title .= ' debug error';
				$headers['X-Priority'] = '1 (High)';
			break;
			
			case DEBUG_TIMING_POINT:
				$title .= ' timig point';
			break;
		} 
		
		$message = '';
		
		if(($user_id = user :: get_id()) != VISITOR_USER_ID)
			$message .= "user id:\t"
								.	"{$user_id}\n"
								. "login:\t\t"  . user :: get_login() . "\n"
								. "e-mail:\t\t" . user :: get_email() . "\n";

		$message .= "ip:\t\t" . sys :: client_ip() . "\n"
							. "request:\t" . REQUEST_URI . "\n"
							. "description:\n" . $description;
				
		send_plain_mail(array(DEVELOPER_EMAIL), $_SERVER['SERVER_ADMIN'] . '<' . $_SERVER['HTTP_HOST'] . '> ' , $title, $message, $headers);

	}

	/*
   Determines the way messages are output, the $output parameter
   is DEBUG_OUTPUT_MESSAGE_SCREEN, DEBUG_OUTPUT_MESSAGE_STORE, DEBUG_OUTPUT_MESSAGE_SEND
  */
	function set_message_output($output)
	{
		if (!isset($this) || get_class($this) != 'debug')
			$debug =& debug::instance();
		else
			$debug =& $this;
		
		$prev_output = $debug->message_output;
		$debug->message_output = $output;
		return $prev_output;
	} 

	function set_store_log($store)
	{
		if (!isset($this) || get_class($this) != 'debug')
			$debug =& debug::instance();
		else
			$debug =& $this;

		$debug->store_log = $store;
	} 

	/*
    Adds a new timing point for the benchmark report.
  */
	function add_timing_point($description = '')
	{
		if (!debug::is_debug_enabled())
			return;
		if (!debug::show_message(DEBUG_SHOW_TIMING_POINT))
			return;
			
		$debug =& debug::instance();

		$time = microtime();
		$tp = array(
			'time' => $time,
			'description' => $description
		);

		$debug->time_points[] = $tp;

		$debug->write(DEBUG_TIMING_POINT, $description);
	} 

	/*
    Writes a debug log message.
  */
	function write($verbosity_level, $string, $code_line = '', $params = array())
	{
		if (!debug::is_debug_enabled())
			return;
		
		if(!in_array($verbosity_level, $this->message_types))
			$verbosity_level = DEBUG_LEVEL_ERROR;
		
		$log_string = $string;		
		$log_string .= (($code_line) ? "\n{$code_line}" : '');
		$log_string .= (count($params) ? "\n" . var_export($params, true) : '');
		
		if ($this->message_output & DEBUG_OUTPUT_MESSAGE_SCREEN)
		{
			print("$verbosity_level:\n $log_string\n\n");
		} 
		
		if ($this->message_output & DEBUG_OUTPUT_MESSAGE_STORE)
		{
			$this->debug_strings[] = array(
				'level' => $verbosity_level,
				'time' => time(),
				'string' => $log_string
			);

			$files =& $this->log_files();
			$file_name = false;
	
			if (isset($files[$verbosity_level]))
				$file_name = $files[$verbosity_level];

			if ($file_name !== false && $this->is_log_file_enabled($verbosity_level))
				$this->_write_file($file_name, $log_string, $verbosity_level);
		}
			
		if($this->message_output & DEBUG_OUTPUT_MESSAGE_SEND)
		{
			$this->_send_mail($log_string, $verbosity_level);
		}
	}
	
	/*
   Writes the log message $string to the file $file_name.
  */
	function _write_file(&$log_file_data, $string, $verbosity_level)
	{
		if (!log::write($log_file_data, $string))
			$this->set_log_file_enabled(false, $verbosity_level);
	} 

	/*
   Enables or disables logging to file for a given message type.
   If $types is not supplied it will do the operation for all types.
  */
	function set_log_file_enabled($enabled, $types = false)
	{
		if ($types === false)
			$types = &$this->message_types();
			
		if (!is_array($types))
			$types = array($types);
			
		foreach ($types as $type)
		{
			$this->log_file_enabled[$type] = $enabled;
		} 
	} 

	/*
   return true if the message type $type has logging to file enabled.
  */
	function is_log_file_enabled($type)
	{
		return $this->log_file_enabled[$type];
	} 

	/*
   return an array with the available message types.
  */
	function message_types()
	{
		return $this->message_types;
	} 

	/*
   Returns an associative array of all the log files used by this class
   where each key is the debug level (DEBUG_LEVEL_NOTICE, DEBUG_LEVEL_WARNING or DEBUG_LEVEL_ERROR or DEBUG_LEVEL).
  */
	function &log_files()
	{
		return $this->log_files;
	} 

	/*
   return true if debug should be enabled.
  */
	function is_debug_enabled()
	{
		return (!defined('DEBUG_ENABLED') || (defined('DEBUG_ENABLED') && constant('DEBUG_ENABLED')));
	} 
	
	function is_console_enabled()
	{
		return (defined('DEBUG_CONSOLE_ENABLED') && constant('DEBUG_CONSOLE_ENABLED'));
	}
	
	function parse_console()
	{
		if(sys :: exec_mode() == 'cli')
			return debug :: parse_cli_console();
		else
			return debug :: parse_html_console();
	}

	/*
    fetches the debug report
  */
	function &parse_html_console($server_file_path = VAR_URL)
	{
		if (!debug::is_debug_enabled())
			return '';

		$debug = &debug::instance();
		$report = &$debug->parse_report_internal(true);

		$js_window = "
						<script language='javascript'>
						<!-- hide this script from old browsers
						
						function show_debug( file_name, title )
						{
							rn = Math.random();
						  debug_window = window.open( file_name + '?rn=' + rn, title, 'top=370,left=550,height=300,width=400,scrollbars,resizable');
						}
						
						show_debug('{$server_file_path}debug.html', 'debug');
															
						//-->
						</script>";

		$header = '<html><head><script>var NEED_TO_FOCUS = false</script><title>debug</title></head><body onload="if(NEED_TO_FOCUS)window.focus();else window.blur()">';
		$footer = '</body></html>';
		$fp = fopen(VAR_DIR . 'debug.html', 'w+');

		fwrite($fp, $header);
		fwrite($fp, $report);
		fwrite($fp, $footer);
		fclose($fp);

		return $js_window;
	} 
	
	function parse_cli_console()
	{
		if (!debug::is_debug_enabled())
			return '';

		$debug = &debug::instance();
		$report = &$debug->parse_report_internal(false);
		
		return $report;
	}

	/*
   Returns the microtime as a float value. $mtime must be in microtime() format.
  */
	function &_time_to_float($mtime)
	{
		$t_time = explode(' ', $mtime);
		ereg("0\.([0-9]+)", '' . $t_time[0], $t1);
		$time = $t_time[1] . '.' . $t1[1];
		return $time;
	} 

	/*
   Sets the time of the start of the script ot $mtime.
   If $mtime is not supplied it gets the current microtime().
   This is used to calculate total execution time and percentages.
  */
	function set_script_start($mtime = false)
	{
		if ($mtime == false)
			$mtime = microtime();
		$time = debug::_time_to_float(microtime());
		$debug = &debug::instance();
		$debug->script_start = $time;
	} 

	/*
    Creates an accumulator group with key $key and group name $name.
    If $name is not supplied name is taken from $key.
  */
	function create_accumulator_group($key, $name = false)
	{
		if (!debug::is_debug_enabled())
			return;
		if ($name == '' || $name === false)
			$name = $key;

		$debug = &debug::instance();

		if (!array_key_exists($key, $debug->time_accumulator_list))
			$debug->time_accumulator_list[$key] = array('name' => $name, 'time' => 0, 'count' => 0, 'is_group' => true, 'in_group' => false);

		if (!array_key_exists($key, $debug->time_accumulator_group_list))
			$debug->time_accumulator_group_list[$key] = array();
	} 

	/*
   Creates a new accumulator entry if one does not already exist and initializes with default data.
   If $name is not supplied name is taken from $key.
   If $in_group is supplied it will place the accumulator under the specified group.
  */
	function create_accumulator($key, $in_group = false, $name = false)
	{
		if (!debug::is_debug_enabled())
			return;
		if ($name == '' || $name === false)
			$name = $key;

		$debug = &debug::instance();

		$is_group = false;
		if (array_key_exists($key, $debug->time_accumulator_list) &&
				array_key_exists($key, $debug->time_accumulator_group_list))
			$is_group = true;

		$debug->time_accumulator_list[$key] =
		array('name' => $name, 'time' => 0, 'count' => 0, 'is_group' => $is_group, 'in_group' => $in_group);

		if ($in_group !== false)
		{
			$group_keys = array();
			if (array_key_exists($in_group, $debug->time_accumulator_group_list))
				$group_keys = $debug->time_accumulator_group_list[$in_group];

			$debug->time_accumulator_group_list[$in_group] = array_unique(array_merge($group_keys, array($key)));

			if (array_key_exists($in_group, $debug->time_accumulator_list))
				$debug->time_accumulator_list[$in_group]['is_group'] = true;
		} 
	} 

	/*
   Starts an time count for the accumulator $key.
   You can also specify a name which will be displayed.
  */
	function accumulator_start($key, $in_group = false, $name = false)
	{
		if (!debug::is_debug_enabled())
			return;

		$debug = &debug::instance();
		if (! array_key_exists($key, $debug->time_accumulator_list))
			$debug->create_accumulator($key, $in_group, $name);

		$accumulator = &$debug->time_accumulator_list[$key];
		$accumulator['temp_time'] = $debug->_time_to_float(microtime());
	} 

	/*
   Stops a previous time count and adds the total time to the accumulator $key.
  */
	function accumulator_stop($key)
	{
		if (!debug::is_debug_enabled())
			return;

		$debug = &debug::instance();
		$stop_time = $debug->_time_to_float(microtime());
		if (! array_key_exists($key, $debug->time_accumulator_list))
		{
			debug::write_warning('Accumulator $key does not exists, run debug::accumulator_start first', 'debug::accumulator_stop');
			return;
		} 
		$accumulator = &$debug->time_accumulator_list[$key];
		$diffTime = $stop_time - $accumulator['temp_time'];
		$accumulator['time'] = $accumulator['time'] + $diffTime;
		++$accumulator['count'];
	} 

	/*
    Prints a full debug report with notice, warnings, errors and a timing report.
  */
	function parse_report_internal($as_html = true)
	{
		$end_time = microtime();
		$return_text = '';
		if ($as_html)
		{
			if ((count($this->debug_strings) - count($this->time_points)) > 0)
				$return_text .= "<script>NEED_TO_FOCUS=1</script>";

			$return_text .= '<table><tr><td>';
			$return_text .= '<table cellspacing=0 cellpadding=1>';
		} 
		
		$counter = 0;
		foreach ($this->debug_strings as $debug)
		{
			$counter++;
			$output_data = $this->output_format[$debug['level']];
			if (is_array($output_data))
			{
				$color = $output_data['color'];
				$name = $output_data['name'];

				if ($as_html)
				{
					$return_text .= "<tr>
													<th align='left'>{$counter})<span style='color:$color'>{$name}:</span></th>
                          </tr>
                          <tr><td><pre>" . htmlspecialchars($debug['string']) . "</pre></td></tr>";
				} 
				else
					$return_text .= "$name: " . $debug['string'] . "\n";
			} 
		} 

		if ($as_html)
		{
			$return_text .= "</table>";
			$return_text .= "<h3>Timing points:</h3>";
			$return_text .= "<table style='border: 1px solid black;' cellspacing='0' cellpadding='1'><tr><th>Checkpoint</th><th>Elapsed</th><th>Rel. Elapsed</th></tr>";
		} 

		$start_time = false;
		$elapsed = 0.00;
		$rel_array = array(-1 => 0.00);

		for ($i=0; $i < count($this->time_points); ++$i)
		{
			$point = $this->time_points[$i];
			$next_point = false;

			if (isset($this->time_points[$i + 1]))
				$next_point = $this->time_points[$i + 1];

			$time = debug :: _time_to_float($point['time']);
			$next_time = false;

			if ($next_point !== false)
				$next_time = debug :: _time_to_float($next_point['time']);
				
			if ($start_time === false)
				$start_time = $time;

			$elapsed = $time - $start_time;
			$rel_elapsed = $rel_array[$i-1];
			$rel_array[] = $next_time - $time;
			
			if ($i % 2 == 0)
				$class = 'timingpoint1';
			else
				$class = 'timingpoint2';

			if ($as_html)
			{
				$return_text .= "<tr><td class='$class'>" . $point['description'] . "</td><td class='$class'>" .
				number_format($elapsed, $this->timing_accuracy) . " sec</td><td class='$class'>" .
				(number_format($rel_elapsed, $this->timing_accuracy) . " sec") . "</td>"
				 . "</tr>";
			} 
			else
			{
				$return_text .= $point['description'] .
				number_format($elapsed, $this->timing_accuracy) . " sec " .
				(number_format($rel_elapsed, $this->timing_accuracy) . " sec") . "\n";
			} 
		}
		
		if (count($this->time_points) > 0)
		{
			$t_time = explode(' ', $end_time);
			ereg("0\.([0-9]+)", '' . $t_time[0], $t1);
			$end_time = $t_time[1] . '.' . $t1[1];

			$total_elapsed = $end_time - $start_time;

			if ($as_html)
			{
				$return_text .= "<tr><td><b>Total runtime:</b></td><td><b>" .
				number_format(($total_elapsed), $this->timing_accuracy) . " sec</b></td><td></td></tr>";
			} 
			else
			{
				$return_text .= "Total runtime: " .
				number_format(($total_elapsed), $this->timing_accuracy) . " sec\n";
			} 
		} 
		else
		{
			if ($as_html)
				$return_text .= "<tr><td> No timing points defined</td><td>";
			else
				$return_text .= "No timing points defined\n";
		} 
		if ($as_html)
			$return_text .= "</table>";

		if ($as_html)
		{
			$return_text .= "<h3>Time accumulators:</h3>";
			$return_text .= "<table style='border: 1px solid black;' cellspacing='0' cellpadding='1'><tr><th>&nbsp;Accumulator</th><th>&nbsp;Elapsed</th><th>&nbsp;Percent</th><th>&nbsp;Count</th><th>&nbsp;Average</th></tr>";
			$i = 0;
		} 

		$script_end_time = debug::_time_to_float(microtime());
		$total_elapsed = $script_end_time - $this->script_start;
		$time_list = $this->time_accumulator_list;
		$groups = $this->time_accumulator_group_list;
		$group_list = array();
		foreach ($groups as $group_key => $key_list)
		{
			if (count($key_list) == 0 && !array_key_exists($group_key, $time_list))
				continue;

			$group_list[$group_key] = array('name' => $group_key);
			if (array_key_exists($group_key, $time_list))
			{
				if ($time_list[$group_key]['time'] != 0)
					$group_list[$group_key]['time_data'] = $time_list[$group_key];

				$group_list[$group_key]['name'] = $time_list[$group_key]['name'];
				unset($time_list[$group_key]);
			} 

			$group_children = array();
			foreach ($key_list as $time_key)
			{
				if (array_key_exists($time_key, $time_list))
				{
					$group_children[] = $time_list[$time_key];
					unset($time_list[$time_key]);
				} 
			} 
			$group_list[$group_key]['children'] = $group_children;
		} 
		if (count($time_list) > 0)
		{
			$group_list['general'] = array('name' => 'general',
				'children' => $time_list);
		} 

		$j = 0;
		foreach ($group_list as $group)
		{
			if ($j % 2 == 0)
				$class = 'timingpoint1';
			else
				$class = 'timingpoint2';

			++$j;
			$group_name = $group['name'];
			$group_children = $group['children'];
			if (count($group_children) == 0 && !array_key_exists('time_data', $group))
				continue;

			if ($as_html)
				$return_text .= "<tr><td class='$class'><b>$group_name</b></td>";
			else
				$return_text .= "Group $group_name: ";

			if (array_key_exists('time_data', $group))
			{
				$group_data = $group['time_data'];
				$group_elapsed = number_format(($group_data['time']), $this->timing_accuracy);
				$group_percent = number_format(($group_data['time'] * 100.0) / $total_elapsed, 1);
				$group_count = $group_data['count'];
				$group_average = number_format(($group_data['time'] / $group_data['count']), $this->timing_accuracy);
				
				if ($as_html)
				{
					$return_text .= ("<td class=\"$class\">$group_elapsed sec</td>" . "<td class=\"$class\" align=\"right\"> $group_percent%</td>" . "<td class=\"$class\" align=\"right\"> $group_count</td>" . "<td class=\"$class\" align=\"right\"> $group_average sec</td>");
				} 
				else
				{
					$return_text .= "$group_elapsed sec ($group_percent%), $group_average avg sec ($group_count)";
				} 
			} 
			elseif ($as_html)
			{
				$return_text .= ("<td class=\"$class\"></td>" . "<td class=\"$class\"></td>" . "<td class=\"$class\"></td>" . "<td class=\"$class\"></td>");
			} 
			if ($as_html)
				$return_text .= "</tr>";
			else
				$return_text .= "\n";

			$i = 0;
			foreach ($group_children as $child)
			{
				$child_name = $child['name'];
				$child_elapsed = number_format(($child['time']), $this->timing_accuracy);
				$child_percent = number_format(($child['time'] * 100.0) / $total_elapsed, $this->percent_accuracy);
				$child_count = $child['count'];
				$child_average = 0.0;

				if ($child_count > 0)
					$child_average = $child['time'] / $child_count;

				$child_average = number_format($child_average, $this->percent_accuracy);

				if ($as_html)
				{
					if ($i % 2 == 0)
						$class = 'timingpoint1';
					else
						$class = 'timingpoint2';
					++$i;

					$return_text .= ("<tr>" . "<td class=\"$class\">$child_name</td>" . "<td class=\"$class\">$child_elapsed sec</td>" . "<td class=\"$class\" align=\"right\">$child_percent%</td>" . "<td class=\"$class\" align=\"right\">$child_count</td>" . "<td class=\"$class\" align=\"right\">$child_average sec</td>" . "</tr>");
				} 
				else
					$return_text .= "$child_name: $child_elapsed sec ($child_percent%), $child_average avg sec ($child_count)";
			} 
		} 
		if ($as_html)
			$return_text .= "<tr><td><b>Total script time:</b></td><td><b>" . number_format(($total_elapsed), $this->timing_accuracy) . " sec</b></td><td></td></tr>";
		else
			$return_text .= "Total script time: " . number_format(($total_elapsed), $this->timing_accuracy) . " sec\n";

		if ($as_html)
		{
			$return_text .= '</table>';
			$return_text .= '</td></tr></table>';
		} 
		return $return_text;
	} 
} 

/*
  Helper function for debug, called whenever a PHP error occurs.
  The error is then handled by the debug class.
*/

function debug_error_handler($errno, $errstr, $errfile, $errline)
{
	if ($GLOBALS['global_debug_recursion_flag'])
	{
		print('Fatal debug error: A recursion in debug error handler was detected, aborting debug message.<br/>');
		$GLOBALS['global_debug_recursion_flag'] = false;
		return;
	} 
	$GLOBALS['global_debug_recursion_flag'] = true;

	$debug = &debug::instance();
	$debug->error_handler($errno, $errstr, $errfile, $errline);

	$GLOBALS['global_debug_recursion_flag'] = false;
} 
$GLOBALS['global_debug_recursion_flag'] = false;

?>