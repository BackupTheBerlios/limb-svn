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

class AbstractDataMapper
{
  //current SimpleTest has limited support for php5 features,
  //error pops up when abstract ones are partially mocked :(

  /*abstract*/ function & _createDomainObject(){}

  /*abstract*/ function & _getFinder(){}

  /*abstract*/ function _doLoad($result_set, &$domain_object){}

  function & findById($id)
  {
    $finder =& $this->_getFinder();
    $rs = $finder->findById($id);

    if (!$rs)
      return null;

    $domain_object =& $this->_createDomainObject();

    $this->_doLoad($rs, $domain_object);

    return $domain_object;
  }

  function save(&$domain_object)
  {
    if($domain_object->getId())
      $this->update($domain_object);
    else
      $this->insert($domain_object);
  }

  function insert(&$domain_object){}

  function update(&$domain_object){}

}

?>
