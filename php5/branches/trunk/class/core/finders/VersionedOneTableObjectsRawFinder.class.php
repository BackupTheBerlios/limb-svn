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
require_once(LIMB_DIR . '/class/core/finders/OneTableObjectsRawFinder.class.php');

abstract class VersionedOneTableObjectsRawFinder extends OneTableObjectsRawFinder
{
  public function find($params=array(), $sql_params=array())
  {
    $sql_params['conditions'][] = ' AND sso.current_version=tn.version';

    return $this->_doParentFind($params, $sql_params);
  }

  public function findByVersion($object_id, $version)
  {
    $sql_params = array();
    $sql_params['conditions'][] = ' AND sso.id=' . $object_id;
    $sql_params['conditions'][] = ' AND tn.version=' . $version;

    return $this->_doParentFind(array(), $sql_params);
  }

  //for mocking
  protected function _doParentFind($params, $sql_params)
  {
    return parent :: find($params, $sql_params);
  }

  protected function _doParentFindCount($sql_params)
  {
    return parent :: findCount($sql_params);
  }

  public function findCount($sql_params=array())
  {
    $sql_params['conditions'][] = ' AND sso.current_version=tn.version';

    return $this->_doParentFindCount($sql_params);
  }
}

?>