<?php

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

  function Exception($code, $message, $backtrace = null)
  {
    $this->code = $code;
    $this->message = $message;
    $this->backtrace = debug_backtrace();

    $bc = $this->backtrace[count($this->backtrace)-1];
    $this->file = @$bc['file'];
    $this->line = @$bc['line'];
    $this->class = @$bc['class'];
    $this->method= @$bc['function'];

    trigger_error($this->toString(), E_USER_WARNING);

    if(isset($GLOBALS['exception_possible_recursion']))
      die("Exception recursion detected(probably exception is not properly caught)!!!\n" .
          var_dump($this->backtrace) .
          $this->toString());

    $GLOBALS['exception_possible_recursion'] = 1;
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

