<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: DAO.class.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/

//Note! abstract class
class RequestResolverResultDAO // implements DAORecord
{
  var $resolver_name;

  function RequestResolverResultDAO($resolver_name)
  {
    $this->resolver_name = $resolver_name;
  }

  function & fetchRecord()
  {
    $toolkit =& Limb :: toolkit();
    $resolver =& $toolkit->getRequestResolver($this->resolver_name);
    if(!is_object($resolver))
      die($this->resolver_name . ' request resolver not set:' . __FILE__ . ' at line '. __LINE__);

    $record = new Dataspace();

    if(!$entity =& $resolver->resolve($toolkit->getRequest()))
      return $record;

    $record->import($entity->export());
    return $record;
  }
}

?>
