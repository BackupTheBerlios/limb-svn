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

class BehaviourFactory
{
  var $_behaviours = array();

  function & instance()
  {
    if (!isset($GLOBALS['BehaviourFactoryGlobalInstance']) || !is_a($GLOBALS['BehaviourFactoryGlobalInstance'], 'BehaviourFactory'))
      $GLOBALS['BehaviourFactoryGlobalInstance'] =& new BehaviourFactory();

    return $GLOBALS['BehaviourFactoryGlobalInstance'];
  }

  function & create($class_name)
  {
    $factory =& BehaviourFactory :: instance();

    if(isset($factory->_behaviours[$class_name]))
      return $factory->_behaviours[$class_name];

    BehaviourFactory :: _includeClassFile($class_name);

    if(catch('FileNotFoundException', $e))//???
      return null;

    $bhvr =& new $class_name();
    $factory->_behaviours[$class_name] =& $bhvr;
    return $bhvr;
  }

  function _includeClassFile($class_name)
  {
    if(class_exists($class_name))
      return;

    $resolver =& Handle :: resolve(getFileResolver('behaviour'));

    $full_path = $resolver->resolve($class_name);

    if(catch('Exception', $e))
      return throw($e);

    include_once($full_path);
  }
}


?>