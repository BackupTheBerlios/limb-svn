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

class meta_redirect_strategy
{
  var $template_path;

  function meta_redirect_strategy($template_path = null)
  {
    $this->template_path = $template_path;
  }

  function redirect(&$http_response, $path)
  {
    $message = $this->_prepare_localized_message($path);

    if($template_file = $this->_find_template())
      $http_response->write($this->_prepare_response_using_template($template_file, $message, $path));
    else
      $http_response->write($this->_prepare_default_response($message, $path));
  }

  function _prepare_response_using_template($template_file, $message, $path)
  {
    $content = file_get_contents($template_file);
    $content = str_replace('{$path}', $path, $content);
    return str_replace('{$message}', $message, $content);
  }

  function _prepare_default_response($message, $path)
  {
    return "<html><head><meta http-equiv=refresh content='0;url={$path}'></head>
            <body bgcolor=white>{$message}</body></html>";
  }

  function _prepare_localized_message($path)
  {
    $message = strings :: get('redirect_message');//???
    return str_replace('%path%', $path, $message);
  }

  function _find_template()
  {
    if(!$this->template_path)
      return null;

    include_once(LIMB_DIR . '/core/template/fileschemes/simpleroot/compiler_support.inc.php');
    return resolve_template_source_file_name($this->template_path);
  }
}

?>
