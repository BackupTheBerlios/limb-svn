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

class SiteObjectBehaviourFactory
{
  var $_behaviours = array();

  function & instance()
  {
    if (!isset($GLOBALS['SiteObjectBehaviourFactoryGlobalInstance']) || !is_a($GLOBALS['SiteObjectBehaviourFactoryGlobalInstance'], 'SiteObjectBehaviourFactory'))
      $GLOBALS['SiteObjectBehaviourFactoryGlobalInstance'] =& new SiteObjectBehaviourFactory();

    return $GLOBALS['SiteObjectBehaviourFactoryGlobalInstance'];
  }

  function & create($class_name)
  {
    $factory =& SiteObjectBehaviourFactory :: instance();

    if(isset($factory->_behaviours[$class_name]))
      return $factory->_behaviours[$class_name];

    SiteObjectBehaviourFactory :: _includeClassFile($class_name);

    $bhvr =& new $class_name();
    $factory->_behaviours[$class_name] =& $bhvr;
    return $bhvr;
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