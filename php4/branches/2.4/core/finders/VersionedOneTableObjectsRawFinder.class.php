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
require_once(LIMB_DIR . '/core/finders/OneTableObjectsRawFinder.class.php');

class VersionedOneTableObjectsRawFinder extends OneTableObjectsRawFinder
{
  function find($params=array(), $sql_params=array())
  {
    $sql_params['conditions'][] = ' AND sso.current_version=tn.version';

    return $this->_doParentFind($params, $sql_params);
  }

  function findByVersion($object_id, $version)
  {
    $sql_params = array();
    $sql_params['conditions'][] = ' AND sso.id=' . $object_id;
    $sql_params['conditions'][] = ' AND tn.version=' . $version;

    return $this->_doParentFind(array(), $sql_params);
  }

  //for mocking
  function _doParentFind($params, $sql_params)
  {
    return parent :: find($params, $sql_params);
  }

  function _doParentFindCount($sql_params)
  {
    return parent :: findCount($sql_params);
  }

  function findCount($sql_params=array())
  {
    $sql_params['conditions'][] = ' AND sso.current_version=tn.version';

    return $this->_doParentFindCount($sql_params);
  }
}

?>