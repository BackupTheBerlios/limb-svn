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
require_once(LIMB_DIR . '/class/lib/util/ini_support.inc.php');
require_once(LIMB_DIR . '/class/template/component.class.php');
require_once(LIMB_DIR . '/class/template/fileschemes/compiler_support.inc.php');
require_once(LIMB_DIR . '/class/template/fileschemes/runtime_support.inc.php');

/**
* Instantiate global variable $template_render and $template_construct as arrays
*/
$template_render = array();
$template_construct = array();

/**
* Public facade for handling templates, dealing with loading, compiling and
* displaying
*/
class template extends component
{
  /**
  * Stored the name of the compiled template file
  */
  protected $codefile;

  protected $file;
  /**
  * Name of function in compiled template which outputs display to screen
  */
  protected $render_function;

  function __construct($file, $resolve_path = true)
  {
    $this->file = $file;

    if($resolve_path)
    {
      if(!$srcfile = resolve_template_source_file_name($file))
        throw new FileNotFoundException('template file not found', $file);
    }
    else
      $srcfile = $file;

    $this->codefile = resolve_template_compiled_file_name($srcfile);

    if (!isset($GLOBALS['template_render'][$this->codefile]))
    {
      if (Limb :: toolkit()->getINI('common.ini')->get_option('force_compile', 'Templates'))
      {
        include_once(LIMB_DIR . '/class/template/compiler/template_compiler.inc.php');
        compile_template_file($file, $resolve_path);
      }

      if(!file_exists($this->codefile))
      {
        include_once(LIMB_DIR . '/class/template/compiler/template_compiler.inc.php');
        compile_template_file($file, $resolve_path);
      }

      $errorlevel = error_reporting();

      if(!defined('DONT_LOWER_TEMPLATES_ERROR_REPORTING'))
      {
        error_reporting($errorlevel &~E_WARNING);
      }

      $parse_error = include_once($this->codefile);

      if(!defined('SET_TEMPLATES_ERROR_REPORTING'))
        error_reporting($errorlevel);

    }
    $this->render_function = $GLOBALS['template_render'][$this->codefile];
    $func = $GLOBALS['template_construct'][$this->codefile];
    $func($this);
  }

  public function get_child($server_id)
  {
    $result = $this->find_child($server_id);
    if (!is_object($result))
    {
      throw new WactException('component not found',
          array('file' => $this->file,
          'server_id' => $server_id));
    }
    return $result;
  }

  /**
  * Outputs the template, calling the compiled templates render function
  */
  public function display()
  {
    $func = $this->render_function;
    $func($this);
  }
}

?>