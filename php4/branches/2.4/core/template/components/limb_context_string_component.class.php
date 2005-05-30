<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: metadata_component.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/template/component.class.php');

class limb_context_string_component extends component
{
  var $resolve_by_path = false;

  function resolve_by_path()
  {
    $this->resolve_by_path = true;
  }

  function resolve_by_identifier()
  {
    $this->resolve_by_path = false;
  }

  function get_string()
  {
    $string_name = $this->_get_string_name();
    $file = $this->_get_file_name();
    $locale_constant = $this->_get_locale();
    echo strings :: get($string_name, $file, constant($locale_constant));
  }

  function _get_string_name()
  {
    if(!$this->resolve_by_path)
      return $this->get('identifier');

    $path = $this->get('path');
    $level = $this->get('level');
    $level_identifiers = explode('/', $path);
    if(isset($level_identifiers[$level]))
      return $level_identifiers[$level];
    else
      return $level_identifiers[0];
  }

  function _get_file_name()
  {
    if(isset($this->attributes['file']))
      return $this->attributes['file'];
    else
      return 'context_help';
  }

  function _get_locale()
  {
    if(isset($this->attributes['locale_type']))
    {
      if(strtolower($this->attributes['locale_type']) == 'content')
        $locale_constant = 'CONTENT_LOCALE_ID';
      else
        $locale_constant = 'MANAGEMENT_LOCALE_ID';
    }
    else
      $locale_constant = 'MANAGEMENT_LOCALE_ID';
  }

}

?>