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

abstract class AbstractDataMapper
{
  //current SimpleTest has limited support for php5 features,
  //error pops up when abstract ones are partially mocked :(

  /*abstract*/ protected function _createDomainObject(){}

  /*abstract*/ protected function _getFinder(){}

  /*abstract*/ protected function _doLoad($result_set, $domain_object){}

  public function findById($id)
  {
    $result_set = $this->_getFinder()->findById($id);

    if (!$result_set)
      return null;

    $domain_object = $this->_createDomainObject();

    $this->_doLoad($result_set, $domain_object);

    return $domain_object;
  }

  public function save($domain_object)
  {
    if($domain_object->getId())
      $this->update($domain_object);
    else
      $this->insert($domain_object);
  }

  public function insert($domain_object)
  {
  }

  public function update($domain_object)
  {
  }

}

?>
