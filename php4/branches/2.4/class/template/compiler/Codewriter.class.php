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
/**
* Provides an API for generating the compiled template.
*/
class Codewriter
{
  const CODE_WRITER_MODE_PHP = 1;
  const CODE_WRITER_MODE_HTML = 2;

  /**
  * String containing the compiled template
  */
  protected $code = '';
  /**
  * The current state of the writer.
  */
  protected $mode;
  /**
  * A prefix to add to the compiled template construct and render functions
  */
  protected $function_prefix = '';
  /**
  * A suffix to add to the compiled template construct and render functions
  */
  protected $function_suffix = 1;
  /**
  * List of files to write include statements for in the compiled template,
  * such as runtime component class files.
  * @access protected
  */
  protected $include_list = array();
  protected $temp_var_name = 1;

  function __construct()
  {
    $this->mode = self :: CODE_WRITER_MODE_HTML;
  }

  /**
  * Puts the writer into PHP mode, writing an opening PHP processing
  * instruction to the template. Does nothing if writer is already
  * in PHP mode
  */
  protected function switchToPhp()
  {
    if ($this->mode == self :: CODE_WRITER_MODE_HTML)
    {
      $this->mode = self :: CODE_WRITER_MODE_PHP;
      $this->code .= '<?php ';
    }
  }

  /**
  * Puts the writer into HTML mode, writing an closing PHP processing
  * instruction to the template. Does nothing if writer is already in
  * HTML mode
  */
  protected function switchToHtml()
  {
    if ($this->mode == self :: CODE_WRITER_MODE_PHP)
    {
      $this->mode = self :: CODE_WRITER_MODE_HTML;
      $this->code .= ' ?>';
    }
  }

  /**
  * Writes some PHP to the compiled template
  */
  public function writePhp($text)
  {
    $this->switchToPhp();
    $this->code .= $text;
  }

  /**
  * Writes some HTML to the compiled template
  */
  public function writeHtml($text)
  {
    $this->switchToHtml();
    $this->code .= $text;
  }

  /**
  * Returns the finished compiled template, adding the include directives
  * at the start of the template
  */
  public function getCode()
  {
    $this->switchToHtml();
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
  */
  public function registerInclude($includefile)
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
  */
  public function beginFunction($ParamList)
  {
    $funcname = 'tpl' . $this->function_prefix . $this->function_suffix++;
    $this->writePhp('function ' . $funcname . $ParamList . "\n{\n");
    return $funcname;
  }

  /**
  * Finish writing a PHP function to the compiled template
  */
  public function endFunction()
  {
    $this->writePhp("\n}\n");
  }

  /**
  * Sets the function prefix
  */
  public function setFunctionPrefix($prefix)
  {
    $this->function_prefix = $prefix;
  }

  public function getTempVariable()
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