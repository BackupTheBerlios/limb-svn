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

/**
* Places the code_writer in PHP mode
*/
define('CODE_WRITER_MODE_PHP', 1);
/**
* Places the code_writer in HTML mode
*/
define('CODE_WRITER_MODE_HTML', 2);

/**
* Provides an API for generating the compiled template.
*/
class codewriter
{
  /**
  * String containing the compiled template
  *
  * @var string
  * @access private
  */
  var $code = '';
  /**
  * The current state of the writer.
  *
  * @var int (default CODE_WRITER_MODE_HTML);
  * @access private
  */
  var $mode = CODE_WRITER_MODE_HTML;
  /**
  * A prefix to add to the compiled template construct and render functions
  *
  * @var string
  * @access private
  */
  var $function_prefix = '';
  /**
  * A suffix to add to the compiled template construct and render functions
  *
  * @var int (default 1)
  * @access private
  */
  var $function_suffix = 1;
  /**
  * List of files to write include statements for in the compiled template,
  * such as runtime component class files.
  *
  * @var array
  * @access private
  */
  var $include_list = array();
  /**
  * ???
  *
  * @var int (default 1)
  * @access private
  */
  var $temp_var_name = 1;

  /**
  * Constructs code_writer, initializing the internal code string
  *
  * @access protected
  */
  function code_writer()
  {
    $this->code = '';
  }

  /**
  * Puts the writer into PHP mode, writing an opening PHP processing
  * instruction to the template. Does nothing if writer is already
  * in PHP mode
  *
  * @return void
  * @access private
  */
  function switch_to_php()
  {
    if ($this->mode == CODE_WRITER_MODE_HTML)
    {
      $this->mode = CODE_WRITER_MODE_PHP;
      $this->code .= '<?php ';
    }
  }

  /**
  * Puts the writer into HTML mode, writing an closing PHP processing
  * instruction to the template. Does nothing if writer is already in
  * HTML mode
  *
  * @return void
  * @access private
  */
  function switch_to_html()
  {
    if ($this->mode == CODE_WRITER_MODE_PHP)
    {
      $this->mode = CODE_WRITER_MODE_HTML;
      $this->code .= ' ?>';
    }
  }

  /**
  * Writes some PHP to the compiled template
  *
  * @param string $ PHP to write
  * @return void
  * @access protected
  */
  function write_php($text)
  {
    $this->switch_to_php();
    $this->code .= $text;
  }

  /**
  * Writes some HTML to the compiled template
  *
  * @param string $ HTML to write
  * @return void
  * @access protected
  */
  function write_html($text)
  {
    $this->switch_to_html();
    $this->code .= $text;
  }

  /**
  * Returns the finished compiled template, adding the include directives
  * at the start of the template
  *
  * @return string
  * @access protected
  */
  function get_code()
  {
    $this->switch_to_html();
    $includecode = '';
    foreach($this->include_list as $includefile)
    {
      $includecode .= "require_once('$includefile');\n";
    }

    if (!empty($includecode))
    {
      $pattern = '/' . preg_quote('<?php ', '/') . '/';
      if (preg_match($pattern, $this->code))
      {
        return preg_replace($pattern, '<?php ' . $includecode, $this->code, 1);
      }
      else
      {
        return '<?php ' . $includecode . '?>';
      }
    }
    else
    {
      return $this->code;
    }
  }

  /**
  * Adds an include file (e.g a runtime component class file) to the
  * internal list. Checks that file has not already been included.
  * <br />Note that the path to the file to be included will need to
  * be in PHP's runtime include path.
  *
  * @param string $ PHP script path/name
  * @return void
  * @access protected
  */
  function register_include($includefile)
  {
    if (!in_array($includefile, $this->include_list))
    {
      $this->include_list[] = $includefile;
    }
  }

  /**
  * Begins writing a PHP function to the compiled template, using the
  * function_prefix and the function_suffix, the latter being post incremented
  * by one.
  *
  * @param string $ parameter string for the function declaration
  * @return string name of the function of form "tpl[Prefix:hash][Suffix:int]
  * @access protected
  */
  function begin_function($ParamList)
  {
    $funcname = 'tpl' . $this->function_prefix . $this->function_suffix++;
    $this->write_php('function ' . $funcname . $ParamList . "\n{\n");
    return $funcname;
  }

  /**
  * Finish writing a PHP function to the compiled template
  *
  * @return void
  * @access protected
  */
  function end_function()
  {
    $this->write_php("\n}\n");
  }

  /**
  * Sets the function prefix
  *
  * @param string $ prefix for function names to be written
  * @return void
  * @access protected
  */
  function set_function_prefix($prefix)
  {
    $this->function_prefix = $prefix;
  }

  /**
  * ???
  *
  * @return string
  * @access protected
  */
  function get_temp_variable()
  {
    $var = $this->temp_var_name++;
    if ($var > 675)
    {
      return chr(65 + ($var/26)/26) . chr(65 + ($var/26)%26) . chr(65 + $var%26);
    }
    elseif ($var > 26)
    {
      return chr(65 + ($var/26)%26) . chr(65 + $var%26);
    }
    else
    {
      return chr(64 + $var);
    }
  }
}

?>