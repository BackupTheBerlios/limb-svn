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
//Inspired by EZpublish(http//ez.no), debug class
require_once(LIMB_DIR . '/class/lib/system/Sys.class.php');

@define('DEBUG_HTTP_CONSOLE_DIR', '/var/');

define('DEBUG_LEVEL_NOTICE',   1);
define('DEBUG_LEVEL_WARNING',  2);
define('DEBUG_LEVEL_ERROR',    3);
define('DEBUG_TIMING_POINT',   4);

class Debug
{
  // String array containing the debug information
  var $debug_strings = array();

  // Array which contains the time points
  var $time_points = array();

  // Array wich contains time accumulators
  var $time_accumulator_list = array();

  // Determines what to do with php errors, ignore, fetch or output
  var $handle_type = 'native';

  // An array of the output_formats for the different debug levels
  var $output_format;

  // An array of log_files used by the debug class with each key being the debug level
  var $log_files;

  // How many places behing . should be displayed when showing times
  var $timing_accuracy = 4;

  // How many places behing . should be displayed when showing percentages
  var $percent_accuracy = 4;

  // A list of message types
  var $message_types;

  // A map with message types and whether they should do file logging.
  var $log_file_enabled;

  // The time when the script was started
  var $script_start;

  function Debug()
  {
    $this->message_types = array(
      DEBUG_LEVEL_NOTICE,
      DEBUG_LEVEL_WARNING,
      DEBUG_LEVEL_ERROR,
      DEBUG_TIMING_POINT
    );

    $this->output_format = array(
      DEBUG_LEVEL_NOTICE => array('color' => 'green',
        'name' => 'Notice'),
      DEBUG_LEVEL_WARNING => array('color' => 'orange',
        'name' => 'Warning'),
      DEBUG_LEVEL_ERROR => array('color' => 'red',
        'name' => 'Error'),
      DEBUG_TIMING_POINT => array('color' => 'blue',
        'name' => 'Timing')
    );

    $this->log_files = array(
      DEBUG_LEVEL_NOTICE => array(VAR_DIR . 'log/',
        'notice.log'),
      DEBUG_LEVEL_WARNING => array(VAR_DIR . 'log/',
        'warning.log'),
      DEBUG_LEVEL_ERROR => array(VAR_DIR . 'log/',
        'error.log'),
      DEBUG_TIMING_POINT => array(VAR_DIR . 'log/',
        'time.log'),
    );

    $this->log_file_enabled = array(
      DEBUG_LEVEL_NOTICE => true,
      DEBUG_LEVEL_WARNING => true,
      DEBUG_LEVEL_ERROR => true,
      DEBUG_TIMING_POINT => false,
    );

    $this->handle_type = 'native';
    $this->old_handler = false;
    $this->script_start = Debug :: _timeToFloat(microtime());
    $this->time_accumulator_list = array();
    $this->time_accumulator_group_list = array();
  }

  function reset()
  {
    $this->debug_strings = array();
    $this->time_accumulator_list = array();
    $this->time_accumulator_group_list = array();
  }

  function & instance()
  {
    if (!isset($GLOBALS['DebugGlobalInstance']) || !is_a($GLOBALS['DebugGlobalInstance'], 'Debug'))
    {
      if(!class_exists('DebugMock'))
        $GLOBALS['DebugGlobalInstance'] =& new Debug();
      else
        $GLOBALS['DebugGlobalInstance'] =& new DebugMock();
    }

    return $GLOBALS['DebugGlobalInstance'];
  }

  function sizeof()
  {
    $inst =& Debug :: instance();
    return sizeof($inst->debug_strings);
  }

  /*
   Determines how PHP errors are handled. If $type is 'trigger' all error messages
   is sent to PHP using trigger_error(), if $type is 'custom' all error messages
   from PHP is fetched using a custom error handler and output as a usual debug message.
   If $type is 'native' there is no error exchange between PHP and debug.
  */
  function setHandleType($type)
  {
    $debug = Debug :: instance();

    if ($type != 'trigger' &&  $type != 'custom')
      $type = 'native';

    if ($type == $debug->handle_type)
      return;

    if ($debug->handle_type == 'custom')
      restore_error_handler();

    switch ($type)
    {
      case 'custom':
        set_error_handler('debugErrorHandler');
      break;

      case 'trigger':
        restore_error_handler();
      break;

      case 'native':
    }
    $debug->handle_type = $type;
  }

  /*
   Handles PHP errors, creates notice, warning and error messages for
   the various PHP error types.
  */
  function errorHandler($errno, $errstr, $errfile, $errline)
  {
    if (error_reporting() == 0) // error-control operator is used
      return;

    if (!Debug :: isDebugEnabled())
      return;

    $str = "$errstr in $errfile on line $errline";
    $errnames =& $GLOBALS['PHP_ERROR_NAMES'];//???

    if (!is_array($errnames))
    {
      $errnames = array(
        E_ERROR => 'E_ERROR',
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
        Debug :: writeError($str, 'PHP');
      }
      break;

      case E_WARNING:
      case E_CORE_WARNING:
      case E_COMPILE_WARNING:
      case E_USER_WARNING:
      {
        Debug :: writeWarning($str, 'PHP');
      }
      break;

      case E_USER_NOTICE:
      case E_NOTICE:
      {
        Debug :: writeNotice($str, 'PHP');
      }
      break;

    }
  }

  /*
    Writes a debug notice.
  */
  function writeNotice($string, $code_line = '', $params = array())
  {
    if (!Debug :: isDebugEnabled())
      return;

    $debug = Debug :: instance();

    if ($debug->handle_type == 'trigger')
      trigger_error($string, E_USER_NOTICE);
    else
      $debug->write(DEBUG_LEVEL_NOTICE, $string, $code_line, $params);
  }

  function writeWarning($string, $code_line = '', $params = array())
  {
    if (!Debug :: isDebugEnabled())
      return;

    $debug = Debug :: instance();

    if ($debug->handle_type == 'trigger')
      trigger_error($string, E_USER_WARNING);
    else
      $debug->write(DEBUG_LEVEL_WARNING, $string, $code_line, $params);
  }

  function writeError($string, $code_line = '', $params = array())
  {
    if (!Debug :: isDebugEnabled())
      return;

    $debug = Debug :: instance();

    if ($debug->handle_type == 'trigger')
      trigger_error($string, E_USER_ERROR);
    else
      $debug->write(DEBUG_LEVEL_ERROR, $string, $code_line, $params);
  }

  function writeException($e)
  {
    if(is_a($e, 'LimbException'))
      Debug :: writeError($e->getMessage(), $e->getFile() . ' : ' . $e->getLine(), $e->getAdditionalParams());
    else
      Debug :: writeError($e->getMessage(), $e->getFile() . ' : ' . $e->getLine());
  }

  function _sendMail($debug_info)
  {
    include_once(LIMB_DIR . '/class/lib/mail/send_plain_mail.inc.php');

    $title = '';
    $headers = array();
    $description = Debug :: _parseTextDebugInfo($debug_info);
    $verbosity_level = $debug_info['level'];

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

    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();

    if(($user_id = $user->getId()) != DEFAULT_USER_ID)
      $message .= "user id:\t"
                .	"{$user_id}\n"
                . "login:\t\t"  . $user->getLogin() . "\n"
                . "e-mail:\t\t" . $user->get('email', '') . "\n";

    if(Sys :: execMode() == 'cli')
      $request_uri = 'cli';
    else
      $request_uri = $_SERVER['REQUEST_URI'];

    $message .= "ip:\t\t" . Sys :: clientIp() . "\n"
              . "request:\t" . $request_uri . "\n"
              . "description:\n" . $description;

    if(Sys :: execMode() == 'cli')
      sendPlainMail(array(DEVELOPER_EMAIL), 'cli' , $title, $message, $headers);
    else
      sendPlainMail(array(DEVELOPER_EMAIL), $_SERVER['SERVER_ADMIN'] . '<' . $_SERVER['HTTP_HOST'] . '> ' , $title, $message, $headers);
  }

  /*
    Adds a new timing point for the benchmark report.
  */
  function addTimingPoint($description = '')
  {
    if (!Debug :: isDebugEnabled())
      return;

    $debug = Debug :: instance();

    $time = microtime();
    $memory = 0;
    if (function_exists('memoryGetUsage'))
        $memory = memoryGetUsage();

    $tp = array(
      'time' => $time,
      'memory_usage' => $memory,
      'description' => $description
    );

    $debug->time_points[] = $tp;

    $debug->write(DEBUG_TIMING_POINT, $description);
  }

  function _parseTextDebugInfo($debug_info)
  {
    $string = $debug_info['string'];
    $code_line = $debug_info['code_line'];
    $params = $debug_info['params'];

    $log_string = $string;
    $log_string .= (($code_line) ? "\n{$code_line}" : '');
    $log_string .= (count($params) ? "\n" . var_export($params, true) : '');

    return $log_string;
  }

  function _parseHtmlDebugInfo($debug_info)
  {
    $string = $debug_info['string'];
    $code_line = $debug_info['code_line'];
    $params = $debug_info['params'];

    $log_string = $string;
    $log_string .= (($code_line) ? "<br>{$code_line}" : '');
    $log_string .= (count($params) ? '<br><pre>' . htmlspecialchars(var_export($params, true)) . '</pre>' : '');

    return $log_string;
  }

  function write($verbosity_level, $string, $code_line = '', $params = array())
  {
    if (!Debug :: isDebugEnabled())
      return;

    if(!in_array($verbosity_level, $this->message_types))
      $verbosity_level = DEBUG_LEVEL_ERROR;

    $debug_info = array(
      'level' => $verbosity_level,
      'time' => time(),
      'string' => $string,
      'code_line' => $code_line,
      'params' => $params
    );

    $this->debug_strings[] = $debug_info;

    if (isset($this->log_files[$verbosity_level]) &&  $this->isLogFileEnabled($verbosity_level))
      $this->_writeFile($this->log_files[$verbosity_level], $debug_info);
  }

  /*
   Writes the log message $string to the file $file_name.
  */
  function _writeFile($file_name, $debug_info)
  {
    include_once(LIMB_DIR . '/class/lib/util/log.class.php');

    if (!log :: write($file_name, Debug :: _parseTextDebugInfo($debug_info)))
      $this->setLogFileEnabled(false, $debug_info['level']);
  }

  /*
   Enables or disables logging to file for a given message type.
   If $types is not supplied it will do the operation for all types.
  */
  function setLogFileEnabled($enabled, $types = false)
  {
    if ($types === false)
      $types = $this->messageTypes();

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
  function isLogFileEnabled($type)
  {
    $inst =& Debug :: instance();
    return $inst->log_file_enabled[$type];
  }

  /*
   return true if debug should be enabled.
  */
  function isDebugEnabled()
  {
    return (!defined('DEBUG_ENABLED') ||  (defined('DEBUG_ENABLED') &&  constant('DEBUG_ENABLED')));
  }

  function isConsoleEnabled()
  {
    return (defined('DEBUG_CONSOLE_ENABLED') &&  constant('DEBUG_CONSOLE_ENABLED'));
  }

  function parseConsole()
  {
    if(Sys :: execMode() == 'cli')
      return Debug :: parseCliConsole();
    else
      return Debug :: parseHtmlConsole();
  }

  /*
    fetches the debug report
  */
  function parseHtmlConsole()
  {
    if (!Debug :: isDebugEnabled())
      return '';

    $inst =& Debug :: instance();
    $report = $inst->_parseReportInternal(true);

    $ip = Sys :: clientIp();
    $js_window = "
            <script language='javascript'>
            <!-- hide this script from old browsers

            function show_debug(file_name, title)
            {
              var debug_path = '" . DEBUG_HTTP_CONSOLE_DIR . "';
              rn = Math.random();
              debug_window = window.open(debug_path + file_name + '?rn=' + rn, title, 'top=370,left=550,height=300,width=400,scrollbars,resizable');
            }

            show_debug('{$ip}-debug.html', 'debug');

            //-->
            </script>";

    $header = '<html><head><script>var NEED_TO_FOCUS = false</script><title>debug</title></head><body onload="if(NEED_TO_FOCUS)window.focus();else window.blur()">';
    $footer = '</body></html>';
    $fp = fopen(VAR_DIR . $ip . '-debug.html', 'w+');

    fwrite($fp, $header);
    fwrite($fp, $report);
    fwrite($fp, $footer);
    fclose($fp);

    return $js_window;
  }

  function parseCliConsole()
  {
    if (!Debug :: isDebugEnabled())
      return '';

    $inst =& Debug :: instance();
    return $inst->_parseReportInternal(false);
  }

  /*
   Returns the microtime as a float value. $mtime must be in microtime() format.
  */
  function _timeToFloat($mtime)
  {
    $t_time = explode(' ', $mtime);
    preg_match("~0\.([0-9]+)~", '' . $t_time[0], $t1);
    $time = $t_time[1] . '.' . $t1[1];
    return $time;
  }

  /*
   Sets the time of the start of the script ot $mtime.
   If $mtime is not supplied it gets the current microtime().
   This is used to calculate total execution time and percentages.
  */
  function setScriptStart($mtime = false)
  {
    if ($mtime == false)
      $mtime = microtime();

    $inst =& Debug :: instance();
    $inst->script_start = Debug :: _timeToFloat($mtime);
  }

  /*
    Creates an accumulator group with key $key and group name $name.
    If $name is not supplied name is taken from $key.
  */
  function createAccumulatorGroup($key, $name = false)
  {
    if (!Debug :: isDebugEnabled())
      return;

    if ($name == '' ||  $name === false)
      $name = $key;

    $debug = Debug :: instance();

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
  function createAccumulator($key, $in_group = false, $name = false)
  {
    if (!Debug :: isDebugEnabled())
      return;

    if ($name == '' ||  $name === false)
      $name = $key;

    $debug = Debug :: instance();

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
  function accumulatorStart($key, $in_group = false, $name = false)
  {
    if (!Debug :: isDebugEnabled())
      return;

    $debug = Debug :: instance();

    if (! array_key_exists($key, $debug->time_accumulator_list))
      $debug->createAccumulator($key, $in_group, $name);

    $accumulator = &$debug->time_accumulator_list[$key];
    $accumulator['temp_time'] = $debug->_timeToFloat(microtime());
  }

  /*
   Stops a previous time count and adds the total time to the accumulator $key.
  */
  function accumulatorStop($key)
  {
    if (!Debug :: isDebugEnabled())
      return;

    $debug = Debug :: instance();

    $stop_time = $debug->_timeToFloat(microtime());
    if (! array_key_exists($key, $debug->time_accumulator_list))
    {
      Debug :: writeWarning('Accumulator $key does not exists, run Debug :: accumulator_start first', 'Debug :: accumulator_stop');
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
  function _parseReportInternal($as_html = true)
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
                          <th align='left'><a name={$counter}>{$counter})<span style='color:$color'>{$name}:</span></th>
                          </tr>
                          <tr><td>" . Debug :: _parseHtmlDebugInfo($debug) . "</td></tr>";
        }
        else
          $return_text .= "$name: " . Debug :: _parseTextDebugInfo($debug);
      }
    }

    if ($as_html)
    {
      $return_text .= "</table>";
      $return_text .= "<h3>Timing points:</h3>";
      $return_text .= "<table style='border: 1px solid black;' cellspacing='0' cellpadding='1'><tr><th>Checkpoint</th><th>Elapsed</th><th>Rel. Elapsed</th><th>Mem</th><th>Rel. Mem</th></tr>";
    }

    $start_time = false;
    $elapsed = 0.00;
    $rel_array = array(-1 => 0.00);
    $rel_memory_array = array(-1 => 0);

    for ($i=0; $i < count($this->time_points); ++$i)
    {
      $point = $this->time_points[$i];

      if (isset($this->time_points[$i + 1]))
        $next_point = $this->time_points[$i + 1];
      else
        $next_point = false;

      $time = Debug :: _timeToFloat($point['time']);
      $memory = $point['memory_usage'];
      $next_time = false;
      $next_memory = 0;

      if ($next_point !== false)
      {
        $next_time = Debug :: _timeToFloat($next_point['time']);
        $next_memory = $next_point['memory_usage'];
      }

      if ($start_time === false)
        $start_time = $time;

      $elapsed = $time - $start_time;
      $rel_elapsed = $rel_array[$i-1];
      $rel_memory_elapsed = $rel_memory_array[$i-1];

      $rel_array[] = $next_time - $time;
      $rel_memory_array[] = $next_memory - $memory;

      if ($i % 2 == 0)
        $class = 'timingpoint1';
      else
        $class = 'timingpoint2';

      if ($as_html)
      {
        $return_text .= "<tr><td class='$class'>" . $point['description'] . "</td><td class='$class'>" .
        number_format($elapsed, $this->timing_accuracy) . "s</td><td class='$class'>" .
        number_format($rel_elapsed, $this->timing_accuracy) . "s</td>" .
        "<td class='$class'>" . number_format($memory / 1024, 2) . "Kb&nbsp;</td>" .
        "<td class='$class'>" . number_format($rel_memory_elapsed / 1024, 2) . "Kb</td>"
        . "</tr>";
      }
      else
      {
        $return_text .= $point['description'] .
        number_format($elapsed, $this->timing_accuracy) . "s " .
        number_format($rel_elapsed, $this->timing_accuracy) . "s " .
        number_format($memory / 1024, 2) . "Kb " .
        number_format($rel_memory_elapsed / 1024, 2) . "Kb" .
        "\n";
      }
    }

    if (count($this->time_points) > 0)
    {
      $t_time = explode(' ', $end_time);
      preg_match("~0\.([0-9]+)~", '' . $t_time[0], $t1);
      $end_time = $t_time[1] . '.' . $t1[1];

      $total_elapsed = $end_time - $start_time;

      if ($as_html)
      {
        $return_text .= "<tr><td><b>Total runtime:</b></td><td><b>" .
        number_format(($total_elapsed), $this->timing_accuracy) . "s</b></td><td></td></tr>";
      }
      else
      {
        $return_text .= "Total runtime: " .
        number_format(($total_elapsed), $this->timing_accuracy) . "s\n";
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

    $script_end_time = Debug :: _timeToFloat(microtime());
    $total_elapsed = $script_end_time - $this->script_start;
    $time_list = $this->time_accumulator_list;
    $groups = $this->time_accumulator_group_list;
    $group_list = array();
    foreach ($groups as $group_key => $key_list)
    {
      if (count($key_list) == 0 &&  !array_key_exists($group_key, $time_list))
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
      if (count($group_children) == 0 &&  !array_key_exists('time_data', $group))
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

function debugErrorHandler($errno, $errstr, $errfile, $errline)
{
  if ($GLOBALS['global_debug_recursion_flag'])
  {
    print('Fatal debug error: A recursion in debug error handler was detected, aborting debug message.<br/>');
    $GLOBALS['global_debug_recursion_flag'] = false;
    return;
  }
  $GLOBALS['global_debug_recursion_flag'] = true;

  $inst =& Debug :: instance();
  $inst->errorHandler($errno, $errstr, $errfile, $errline);

  $GLOBALS['global_debug_recursion_flag'] = false;
}
$GLOBALS['global_debug_recursion_flag'] = false;

?>