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
class exception
{
  /** The nested "cause" exception. */
  var $cause;

	var $code;
	var $message;
	var $context;

	var $class;
	var $file;
	var $line;
	var $method; 
	// debug backtrace
	var $backtrace;
	var $catched = false;

	function exception($code, $p1, $p2 = null)
	{	
    $cause = null;

    if ($p2 !== null) 
    {
      $message = $p1;
      $cause = $p2;
    }
    else
    {
      if (is_a($p1, 'exception')) 
      {
        $message = '';
        $cause = $p1;
      } 
      else
      	$message = $p1;
    }

		$this->code = $code;
		$this->message = $message;
		$this->backtrace = debug_backtrace();

		$bc = $this->backtrace[1];
		$this->file = @$bc['file'];
		$this->line = @$bc['line'];
		$this->class = @$bc['class'];
		$this->method = @$bc['function'];

    if ($cause !== null) 
    {
      $this->backtrace = $cause->backtrace;
      $this->cause = $cause;
      $this->message .= " [wrapped: " . $cause->get_message() ."]";
    }
    
    trigger_error($this->to_string(), E_USER_WARNING);
	} 
		
	function catch()
	{
		$this->catched = true;
	}

  function get_cause()
  {
    return $this->cause;
  }
  
	function get_message()
	{
		return $this->message;
	} 

	function get_code()
	{
		return $this->code;
	} 

	/**
	* Returns a string representation fitting for debug output.
	* 
	* @return string String representation of the exception
	* @author manuel holtgrewe <purestorm at teforge dot org> 
	* @access public 
	*/
	function to_string()
	{
		$result = "[exception, message=\"" . $this->message . "\", " . "code=\"" . $this->code . "\", " . "file=\"" . $this->file . "\", " . "line=\"" . $this->line . "\"]";
		return $result;
	} 

	/**
	* 
	* @author alex black, enigma@turingstudio.com 
	* @access public 
	*/
	function get_file()
	{
		return $this->file;
	} 

	function get_line()
	{
		return $this->line;
	} 

	function get_context($html = false)
	{
		$str = ($html ? "<h3>[Context]</h3>\n" : "[Context]\n");

		if (! file_exists($this->file))
		{
			$str .= "Context cannot be shown - ($this->file) does not exist\n";
			return $str;
		} 
		if ((! is_int($this->line)) || ($this->line <= 0))
		{
			$str .= "Context cannot be shown - ($this->line) is an invalid line number";
			return $str;
		} 

		$lines = file($this->file); 
		// get the source ## core dump in windows, scrap colour highlighting :-(
		// $source = highlight_file($this->file, true);
		// $this->lines = split("<br />", $source);
		// get line numbers
		$start = $this->line - 6; // -1 including error line
		$finish = $this->line + 5; 
		// get lines
		if ($start < 0)
		{
			$start = 0;
		} 
		if ($start >= count($lines))
		{
			$start = count($lines) -1;
		} 
		for ($i = $start; $i < $finish; $i++)
		{ 
			// highlight line in question
			if ($i == ($this->line -1))
			{
				$context_lines[] = '<font color="red"><b>' . ($i + 1) . "\t" . strip_tags($lines[$this->line -1]) . '</b></font>';
			} 
			else
			{
				$context_lines[] = '<font color="black"><b>' . ($i + 1) . "</b></font>\t" . @$lines[$i];
			} 
		} 

		$str .= trim(join("<br />\n", $context_lines)) . "<br />\n";
		return $str;
	} 

	function get_backtrace($html = false)
	{
		$str = ($html ? "<h3>[Backtrace]</h3>\n" : "[Backtrace]\n");

		foreach($this->backtrace as $bc)
		{
			if (isset($bc['class']))
			{
				$s = ($html ? "<b>%s</b>" : "%s") . "::";
				$str .= sprintf($s, $bc['class']);
			} 
			if (isset($bc['function']))
			{
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
			if (isset($bc['file']))
			{
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

