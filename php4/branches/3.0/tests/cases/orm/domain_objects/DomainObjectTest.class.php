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
require_once(LIMB_DIR . '/core/orm/DomainObject.class.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');

class DomainObjectTest extends LimbTestCase
{
  var $object;

  function DomainObjectTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->object = new DomainObject();
  }

  function tearDown()
  {
  }
}

?>