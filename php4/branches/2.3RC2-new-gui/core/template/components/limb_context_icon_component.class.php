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

class limb_context_icon_component extends component
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

  function get_icon()
  {
    $image_name = $this->_get_image_name();
    $image_path = $this->_resolve_image_path($image_name);
    $image_alt = $this->_get_image_alt($image_name);
    $image_title = $this->_get_image_title($image_name);
    echo "<img src='{$image_path}' alt='{$image_alt}' title='{$image_title}'>";
  }

  function _get_image_name()
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
  
  function _resolve_image_path($image_name)
  {
    if(!($image_variation = $this->get('variation')))
      $image_variation = '48';

    if(empty($image_name))
      return SHARED_IMG_URL . '/icon/cp/' . $image_variation . '/default.gif';
    
    $project_file = '/design/main/images/icon/cp/'. $image_variation . '/' . $image_name . '.gif';
    $common_file = '/icon/cp/'. $image_variation . '/' . $image_name . '.gif';

    if(file_exists(PROJECT_DIR  . $project_file))
      return $project_file;
    else if(file_exists(SHARED_DIR . '/images/' . $common_file))
      return SHARED_IMG_URL . $common_file;
    else
      return SHARED_IMG_URL . '/icon/cp/' . $image_variation . '/default.gif';
  }
  
  function _get_image_alt($image_name)
  {
    return strings :: get('alt_' . $image_name, 'icons');
  }

  function _get_image_title($image_name)
  {
    return strings :: get('title_' . $image_name, 'icons');
  }
}

?>