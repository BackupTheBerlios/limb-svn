<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: EditSimpleObjectCommand.class.php 1186 2005-03-23 09:47:34Z seregalimb $
*
***********************************************************************************/

class PutRequestResolverResultToContextCommand
{
  var $field_name;
  var $resolver_name;

  function PutRequestResolverResultToContextCommand($resolver_name, $field_name)
  {
    $this->resolver_name = $resolver_name;
    $this->field_name = $field_name;
  }

  function perform(&$context)
  {
    $toolkit =& Limb :: toolkit();
    $resolver =& $toolkit->getRequestResolver($this->resolver_name);
    if(!is_object($resolver))
      return LIMB_STATUS_ERROR;

    $request =& $toolkit->getRequest();

    if(!$entity =& $resolver->resolve($request))
      return LIMB_STATUS_ERROR;

    $context->setObject($this->field_name, $entity);

    return LIMB_STATUS_OK;
  }
}

?>
