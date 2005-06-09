<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: DefaultObjectIdentifierGenerator.class.php 1343 2005-06-01 08:16:13Z pachanga $
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
