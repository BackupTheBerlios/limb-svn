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
require_once(LIMB_DIR . '/core/site_objects/DefaultSiteObjectLocalePolicy.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/site_objects/SiteObject.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                      'DefaultSiteObjectLocalePolicyTestToolkit',
                      array('constant'));

class DefaultSiteObjectLocalePolicyTest extends LimbTestCase
{
  var $policy;

  function DefaultSiteObjectLocalePolicyTest()
  {
    parent :: LimbTestCase('default site object locale policy tests');
  }

  function setUp()
  {
    $this->toolkit = new DefaultSiteObjectLocalePolicyTestToolkit($this);
    $this->policy = new DefaultSiteObjectLocalePolicy();
    $this->db =& new SimpleDb(LimbDbPool :: getConnection());

    $this->_cleanUp();

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->_cleanUp();

    Limb :: popToolkit();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_site_object');
    $this->db->delete('sys_site_object_tree');
  }

  function testGetParentLocaleIdDefault()
  {
    $this->toolkit->setReturnValue('constant',
                                   $locale_id  = 'ge',
                                   array('DEFAULT_CONTENT_LOCALE_ID'));

    $site_object = new SiteObject();

    $this->policy->assign($site_object);
    $this->assertEqual($site_object->getLocaleId(), $locale_id);
  }

  function testGetParentLocaleId()
  {
    $this->db->insert('sys_site_object', array('locale_id' => $locale_id = 'ru',
                                               'id' => 200));

    $this->db->insert('sys_site_object_tree', array('object_id' => 200,
                                                    'id' => $parent_node_id = 300));

    $site_object = new SiteObject();
    $site_object->setParentNodeId($parent_node_id);

    $this->policy->assign($site_object);
    $this->assertEqual($site_object->getLocaleId(), $locale_id);
  }
}

?>