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
require_once(LIMB_DIR . '/core/datasource/datasource.class.php');
require_once(LIMB_DIR . '/core/lib/system/fs.class.php');

class site_objects_classes_options_datasource extends datasource
{
  function get_default_option()
  {
    return 0;
  }

  function get_options_array()
  {
    $result = array();

    $this->_add_project_site_objects($result);

    if($result)
      array_unshift($result, '---');

    $result[-1] = '---';

    $this->_add_limb_site_objects($result);

    return $result;
  }

  function _add_limb_site_objects(&$result)
  {
    //media object can't really be a valid site object(refactor!!!)
    $items = fs :: find_subitems(LIMB_DIR . '/core/model/site_objects/', 'f', '~media_object~', false);

    foreach($items as $item)
    {
      $class = $this->_clean_class_path($item);
      $result[$class] = $class;
    }
  }

  function _add_project_site_objects(&$result)
  {
    $items = fs :: find_subitems(PROJECT_DIR . '/core/model/site_objects/', 'f', '', false);

    foreach($items as $item)
    {
      $class = $this->_clean_class_path($item);
      $result[$class] = $class;
    }
  }

  function _clean_class_path($class_path)
  {
    preg_match('~^([^\.]*)\.class\.php$~', $class_path, $matches);
    return $matches[1];
  }
}
?>