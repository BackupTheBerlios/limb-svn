<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ObjectIdentifierGenerator.interface.php 1103 2005-02-14 15:16:43Z pachanga $
*
***********************************************************************************/

class DefaultObjectIdentifierGenerator
{
  function generate(&$object)
  {
    return $object->get('identifier');
  }
}

?>
