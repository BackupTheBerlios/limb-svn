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
require_once(LIMB_DIR . '/core/system/objects_support.inc.php');

class SearchTextNormalizerFactory
{
  function create($class_name)
  {
    SearchTextNormalizerFactory :: _includeClassFile($class_name);

    return new $class_name();
  }

  function _includeClassFile($class_name)
  {
    if(class_exists($class_name))
      return;

    include_once(dirname(__FILE__) . '/' . $class_name . '.class.php');
  }
}
?>