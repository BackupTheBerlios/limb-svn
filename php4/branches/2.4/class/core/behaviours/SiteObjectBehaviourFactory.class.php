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

abstract class SiteObjectBehaviourFactory
{
  static protected $_behaviours = array();

  static public function create($class_name)
  {
    if(isset(self :: $_behaviours[$class_name]))
      return self :: $_behaviours[$class_name];

    self :: _includeClassFile($class_name);

    self :: $_behaviours[$class_name] = new $class_name();
    return self :: $_behaviours[$class_name];
  }

  static protected function _includeClassFile($class_name)
  {
    if(class_exists($class_name))
      return;

    resolveHandle($resolver =& getFileResolver('behaviour'));

    $full_path = $resolver->resolve($class_name);

    include_once($full_path);
  }
}


?>