<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: PackagesInfoTest.class.php 1028 2005-01-18 11:06:55Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/permissions/JIPProcessor.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');

class JIPProcessorTest extends LimbTestCase
{
  function JIPProcessorTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function testProcess()
  {
    $actions = array('display' => array(),
                     'edit' => array('jip' => true, 'popup' => 1),
                     'create' => array('jip' => true));

    $object = new Object();
    $object->set('_node_path', $path = 'whatever');
    $object->set('actions', $actions);

    $processor = new JIPProcessor();
    $processor->process($object);


    $jip_actions = array('edit' => array('jip' => true,
                                         'name' => 'edit',
                                         'jip_href' => $path . '?action=edit&popup=1',
                                         'popup' => 1),
                         'create' => array('jip' => true,
                                           'name' => 'create',
                                           'jip_href' => $path . '?action=create'));

    $this->assertEqual($object->get('jip_actions'), $jip_actions);
  }
}

?>