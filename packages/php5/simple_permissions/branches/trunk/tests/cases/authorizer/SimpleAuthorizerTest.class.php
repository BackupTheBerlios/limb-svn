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
require_once(LIMB_DIR . '/class/core/site_objects/SiteObjectController.class.php');
require_once(LIMB_DIR . '/class/core/behaviours/SiteObjectBehaviour.class.php');
require_once(dirname(__FILE__) . '/../../../SimpleAuthorizer.class.php');
require_once(LIMB_DIR . '/class/core/permissions/User.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');
require_once(LIMB_DIR . '/class/db_tables/DbTableFactory.class.php');
require_once(dirname(__FILE__) . '/../../../AccessPolicy.class.php');

class SimpleAuthorizerTestBehaviourTestVersion extends SiteObjectBehaviour
{
  public function getDisplayActionProperties(){}
  public function getCreateActionProperties(){}
  public function getEditActionProperties(){}
  public function getPublishActionProperties(){}
  public function getDeleteActionProperties(){}
}

Mock :: generate('SimpleAuthorizerTestBehaviourTestVersion');

Mock :: generatePartial('SimpleAuthorizer',
                        'SimpleAuthorizerTestVersion',
                        array('getUserAccessorIds'));

Mock :: generatePartial('SimpleAuthorizer',
                        'SimpleAuthorizerTestVersion2',
                        array('getUserAccessorIds',
                              'getBehaviourAccessibleActions',
                              '_getBehaviour'));

Mock :: generate('SiteObjectController');

class SimpleAuthorizerTest extends LimbTestCase
{
  var $db;

  function setUp()
  {
    $this->cleanUp();
  }

  function tearDown()
  {
    User :: instance()->logout();

    $this->cleanUp();
  }

  function cleanUp()
  {
    $db = DbFactory :: instance();

    $db->sqlDelete('sys_object_access');
    $db->sqlDelete('sys_action_access');
  }

  function testGetAccessibleObjectIdsNoUserAccessorIds()
  {
    $authorizer = new SimpleAuthorizerTestVersion($this);

    $this->assertEqual($authorizer->getAccessibleObjectIds(array(300, 303)), array());
  }

  function testGetAccessibleObjectIdsNoDbRecords()
  {
    $authorizer = new SimpleAuthorizerTestVersion($this);
    $authorizer->setReturnValue('getUserAccessorIds', array(100, 200));
    $this->assertEqual($authorizer->getAccessibleObjectIds(array(300, 303)), array());
  }

  function testGetAccessibleObjectIdsOk()
  {
    $authorizer = new SimpleAuthorizerTestVersion($this);
    $authorizer->expectOnce('getUserAccessorIds');
    $authorizer->setReturnValue('getUserAccessorIds', array(100, 200));

    $db_table = DbTableFactory :: create('SysObjectAccess');

    $db_table->insert(array('id' => 1,
                           'object_id' => 300,
                           'access' => 1,
                           'accessor_id' => 100, // this one is accessible
                           'accessor_type' => AccessPolicy :: ACCESSOR_TYPE_GROUP));

    $db_table->insert(array('id' => 2,
                           'object_id' => 302,
                           'access' => 1,
                           'accessor_id' => 200, // this one is accessible
                           'accessor_type' => AccessPolicy :: ACCESSOR_TYPE_GROUP));

    $db_table->insert(array('id' => 3,
                           'object_id' => 301,
                           'access' => 1,
                           'accessor_id' => 101, // this one is not accessible
                           'accessor_type' => AccessPolicy :: ACCESSOR_TYPE_GROUP));

    $db_table->insert(array('id' => 4,
                           'object_id' => 305, // this one is not accessible
                           'access' => 1,
                           'accessor_id' => 100,
                           'accessor_type' => AccessPolicy :: ACCESSOR_TYPE_GROUP));

    $result = array(300, 302);
    $this->assertEqual($authorizer->getAccessibleObjectIds(array(300, 301, 302, 303)), $result);

    $authorizer->tally();
  }

  function testGetBehaviourAccesssibleActionsNoUserAccessorIds()
  {
    $authorizer = new SimpleAuthorizerTestVersion($this);
    $authorizer->setReturnValue('getUserAccessorIds', array(100, 200));

    $behaviour_id = 10;
    $this->assertEqual($authorizer->getBehaviourAccessibleActions($behaviour_id), array());
  }

  function testGetBehaviourAccesssibleActionsNoDbRecords()
  {
    $authorizer = new SimpleAuthorizerTestVersion($this);
    $authorizer->setReturnValue('getUserAccessorIds', array(100, 200));

    $behaviour_id = 10;
    $this->assertEqual($authorizer->getBehaviourAccessibleActions($behaviour_id), array());
  }

  function testGetBehaviourAccesssibleActionsOk()
  {
    $authorizer = new SimpleAuthorizerTestVersion($this);
    $authorizer->expectOnce('getUserAccessorIds');
    $authorizer->setReturnValue('getUserAccessorIds', array(100, 200));

    $behaviour_id = 10;

    $db_table = DbTableFactory :: create('SysActionAccess');

    $db_table->insert(array('id' => 1,
                           'behaviour_id' => $behaviour_id,
                           'action_name' => 'create',
                           'accessor_id' => 100, // this one is accessible
                           'accessor_type' => AccessPolicy :: ACCESSOR_TYPE_GROUP));

    $db_table->insert(array('id' => 2,
                           'behaviour_id' => $behaviour_id,
                           'action_name' => 'delete',
                           'accessor_id' => 200, // this one is accessible too
                           'accessor_type' => AccessPolicy :: ACCESSOR_TYPE_GROUP));

    $db_table->insert(array('id' => 3,
                            'behaviour_id' => $behaviour_id,
                            'action_name' => 'edit',
                            'accessor_id' => 101, // this one is NOT accessible
                            'accessor_type' => AccessPolicy :: ACCESSOR_TYPE_GROUP));

    $db_table->insert(array('id' => 4,
                            'behaviour_id' => 12, // this one is NOT accessible too
                            'action_name' => 'publich',
                            'accessor_id' => 101,
                            'accessor_type' => AccessPolicy :: ACCESSOR_TYPE_GROUP));

    $result = array('create', 'delete');

    $this->assertEqual($authorizer->getBehaviourAccessibleActions($behaviour_id), $result);

    $authorizer->tally();
  }

  function testAssignActionsOk()
  {
    $behaviour_id = 10;

    $behaviour = new MockSimpleAuthorizerTestBehaviourTestVersion($this);

    $behaviour->setReturnValue('getDisplayActionProperties', array('some display action data'));
    $behaviour->setReturnValue('getCreateActionProperties', array('some create action data'));
    $behaviour->setReturnValue('getEditActionProperties', array());
    $behaviour->setReturnValue('getPublishActionProperties', array());
    $behaviour->setReturnValue('getDeleteActionProperties', array());

    $actions_list = array('display', 'create', 'edit', 'publish', 'delete');
    $behaviour->setReturnValue('getActionsList', $actions_list);

    $authorizer = new SimpleAuthorizerTestVersion2($this);
    $authorizer->expectOnce('getBehaviourAccessibleActions');
    $authorizer->setReturnValue('getBehaviourAccessibleActions',
                                array('create', 'display', 'edit', 'delete'),
                                array($behaviour_id));

    $authorizer->setReturnValue('_getBehaviour', $behaviour, array('test_behaviour'));

    $objects_to_assign_actions = array(
      1 => array(
        'id' => 300,
        'behaviour_id' => $behaviour_id,
        'behaviour' => 'test_behaviour',
      ),
    );

    $authorizer->assignActionsToObjects($objects_to_assign_actions);

    $obj = reset($objects_to_assign_actions);
    $this->assertEqual(sizeof($obj['actions']), 4);

    $this->assertEqual($obj['actions'],
      array(
        'create' => array('some create action data'),
        'display' => array('some display action data'),
        'edit' => array(),
        'delete' => array()
      )
    );

    $behaviour->tally();
    $authorizer->tally();
  }
}

?>