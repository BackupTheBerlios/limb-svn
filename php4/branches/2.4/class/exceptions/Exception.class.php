<?php

//register_shutdown_function('writeUnhandledExceptions');

function writeUnhandledExceptions()
{
  if(!isset($GLOBALS['global_exception']))
    return;

  include_once(LIMB_DIR . '/class/error/Debug.class.php');

  Debug :: writeException($GLOBALS['global_exception']);
}

class Exception
{
  var $code;
  var $message;
  var $context;

  var $class;
  var $file;
  var $line;
  var $method;

  var $backtrace;

  function Exception($message, $code = 0, $backtrace = null)
  {
    $this->code = $code;
    $this->message = $message;
    $this->backtrace = debug_backtrace();

    $bc = $this->backtrace[count($this->backtrace)-1];
    $this->file = @$bc['file'];
    $this->line = @$bc['line'];
    $this->class = @$bc['class'];
    $this->method = @$bc['function'];
  }

  function getMessage()
  {
    return $this->message;
  }

  function getCode()
  {
    return $this->code;
  }

  function toString()
  {
    $result = "[Exception, message=\"" . $this->message . "\", " .
              "code=\"" . $this->code. "\", " .
              "file=\"" . $this->file . "\", " .
              "line=\"" . $this->line . "\"]";
    return $result;
  }

  function getFile()
  {
    return $this->file;
  }

  function getLine()
  {
    return $this->line;
  }

  function getContext($html = false)
  {
    $str = ($html ? "<h3>[Context]</h3>\n" : "[Context]\n");

    if (! file_exists($this->file)) {
      $str .= "Context cannot be shown - ($this->file) does not exist\n";
      return $str;
    }
    if ((! is_int($this->line)) || ($this->line <= 0)) {
      $str .= "Context cannot be shown - ($this->line) is an invalid line number";
      return $str;
    }

    $lines = file($this->file);
    //  get the source ## core dump in windows, scrap colour highlighting :-(
    //  $source = highlight_file($this->file, true);
    //  $this->lines = split("<br />", $source);
    //  get line numbers
    $start = $this->line - 6; // -1 including error line
    $finish = $this->line + 5;
    //  get lines
    if ($start < 0) {
        $start = 0;
    }
    if ($start >= count($lines)) {
        $start = count($lines) -1;
    }
    for ($i = $start; $i < $finish; $i++) {
        //  highlight line in question
        if ($i == ($this->line -1)) {
            $context_lines[] = '<font color="red"><b>' . ($i + 1) .
                "\t" . strip_tags($lines[$this->line -1]) . '</b></font>';
        } else {
            $context_lines[] = '<font color="black"><b>' . ($i + 1) .
                "</b></font>\t" . @$lines[$i];
        }
    }

    $str .= trim(join("<br />\n", $context_lines)) . "<br />\n";
    return $str;
  }

  function getBacktrace($html = false)
  {
    $str = ($html ? "<h3>[Backtrace]</h3>\n" : "[Backtrace]\n");

    foreach($this->backtrace as $bc)
    {
      if (isset($bc['class'])) {
        $s = ($html ? "<b>%s</b>" : "%s") . "::";
        $str .= sprintf($s, $bc['class']);
      }
      if (isset($bc['function'])) {
        $s = ($html ? "<b>%s</b>" : "%s");
        $str .= sprintf($s, $bc['function']);
      }

      $str .= ' (';

      if (isset($bc['args']))
      {
        foreach($bc['args'] as $arg)
        {
          $s = ($html ? "<i>%s</i>, " : "%s, ");
          $str .= sprintf($s, gettype($arg));
        }
        $str = substr($str, 0, -2);
      }

      $str .= ')';
      $str .= ': ';
      $str .= '[ ';
      if (isset($bc['file'])) {
        $dir = substr(dirname($bc['file']), strrpos(dirname($bc['file']), '/') + 1);
        $file = basename($bc['file']);
        if ($html) $str .= "<a href=\"file:/" . $bc['file'] . "\">";
        $str .= $dir . '/' . $file;
        if ($html) $str .= "</a>";
      }
      $str .= isset($bc['line']) ? ', ' . $bc['line'] : '';
      $str .= ' ] ';
      $str .= ($html ? "<br />\n" : "\n");
    }

    return $str;
  }
}

//idea taken from binarycloud
function throw(&$exception)
{
  if (isset($GLOBALS['global_exception']))
  {
    if ($GLOBALS['global_exception'] !== null)
    {
      // take drastic actions, will be handled better in php5
      printf('Trying to throw an Exception (%s) though last exception (%s) was not caught',
             $exception->toString(),
             $GLOBALS['global_exception']->toString());

      if(php_sapi_name() == 'cli')
        $use_html = false;
      else
        $use_html = true;

      print $GLOBALS['global_exception']->getBacktrace($use_html);
      die();//???
    }
  }

  $GLOBALS['global_exception'] =& $exception;
}

function catch($type, &$result)
{
  if (!isset($GLOBALS['global_exception']) || $GLOBALS['global_exception'] === null)
    return false;

  if (!is_a($GLOBALS['global_exception'], $type))
    return false;
  else
    $result = $GLOBALS['global_exception'];

  $GLOBALS['global_exception'] = null;
  return true;
}

