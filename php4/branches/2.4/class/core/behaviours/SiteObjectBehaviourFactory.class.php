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
  var $_behaviours = array();

  function create($class_name)
  {
    if(isset(SiteObjectBehaviourFactory :: $_behaviours[$class_name]))
      return SiteObjectBehaviourFactory :: $_behaviours[$class_name];

    SiteObjectBehaviourFactory :: _includeClassFile($class_name);

    SiteObjectBehaviourFactory :: $_behaviours[$class_name] = new $class_name();
    return SiteObjectBehaviourFactory :: $_behaviours[$class_name];
  }

  function _includeClassFile($class_name)
  {
    if(class_exists($class_name))
      return;

    resolveHandle($resolver =& getFileResolver('behaviour'));

    $full_path = $resolver->resolve($class_name);

    include_once($full_path);
  }
}


?>