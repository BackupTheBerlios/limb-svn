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
require_once(dirname(__FILE__) . '/../../../AccessPolicy.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');
require_once(LIMB_DIR . '/class/db_tables/DbTableFactory.class.php');

class AccessPolicyTest extends LimbTestCase
{
  var $ac = null;

  function setUp()
  {
    $this->_cleanUp();
    $this->ac = new AccessPolicy();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $db =& DbFactory :: instance();
    $db->sqlDelete('sys_action_access_template');
    $db->sqlDelete('sys_action_access_template_item');

    $db->sqlDelete('sys_action_access');
    $db->sqlDelete('sys_object_access');
  }

  function testGetAccessTemplates()
  {
    $db_table = DbTableFactory :: create('SysActionAccessTemplate');
    $item_db_table = DbTableFactory :: create('SysActionAccessTemplateItem');

    $db_table->insert(array('id'=> 1, 'action_name' => 'create', 'behaviour_id' => 11, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 2, 'action_name' => 'publish', 'behaviour_id' => 11, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 3, 'action_name' => 'edit', 'behaviour_id' => 11, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_GROUP));

    $item_db_table->insert(array('template_id' => 1, 'accessor_id' => 200, 'access' => 1));
    $item_db_table->insert(array('template_id' => 1, 'accessor_id' => 210, 'access' => 1));
    $item_db_table->insert(array('template_id' => 2, 'accessor_id' => 200, 'access' => 1));
    $item_db_table->insert(array('template_id' => 2, 'accessor_id' => 210, 'access' => 0));
    $item_db_table->insert(array('template_id' => 3, 'accessor_id' => 200, 'access' => 1));

    $template = $this->ac->getAccessTemplates($behaviour_id = 11, ACCESS_POLICY_ACCESSOR_TYPE_USER);

    $this->assertEqual(sizeof($template), 2);

    $this->assertEqual($template,
      array(
        'create' => array(
            200 => 1,
            210 => 1,
        ),
        'publish' => array(
            200 => 1,
            210 => 0,
        ),
      )
    );
  }

  function testGetActionsAccess()
  {
    $db_table = DbTableFactory :: create('SysActionAccess');
    $db_table->insert(array('id'=> 1, 'behaviour_id' => 11, 'action_name' => 'create', 'accessor_id' => 10, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 2, 'behaviour_id' => 11, 'action_name' => 'edit', 'accessor_id' => 10, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 3, 'behaviour_id' => 11, 'action_name' => 'publish', 'accessor_id' => 20, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_GROUP));

    $actions = $this->ac->getActionsAccess(11, ACCESS_POLICY_ACCESSOR_TYPE_USER);
    $this->assertEqual($actions, array(10 => array('create' => 1, 'edit' => 1)));

    $actions = $this->ac->getActionsAccess(11, ACCESS_POLICY_ACCESSOR_TYPE_GROUP);
    $this->assertEqual($actions, array(20 => array('publish' => 1)));
  }

  function testGetObjectsAccessByIds()
  {
    $db_table = DbTableFactory :: create('SysObjectAccess');

    $db_table->insert(array('id'=> 1, 'object_id' => 11, 'access' => 1, 'accessor_id' => 10, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 2, 'object_id' => 11, 'access' => 1, 'accessor_id' => 11, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 3, 'object_id' => 12, 'access' => 1, 'accessor_id' => 10, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 4, 'object_id' => 13, 'access' => 0, 'accessor_id' => 10, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 5, 'object_id' => 13, 'access' => 1, 'accessor_id' => 20, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_GROUP));

    $access_records = $this->ac->getObjectsAccessByIds(array(11, 12, 13, 14), ACCESS_POLICY_ACCESSOR_TYPE_USER);

    $this->assertEqual($access_records,
      array(
        '11' => array(
          10 => 1,
          11 => 1,
        ),
        '12' => array(
          10 => 1,
        ),
        '13' => array(
          10 => 0,
        ),
      )
    );

    $access_records = $this->ac->getObjectsAccessByIds(array(11, 12, 13, 14), ACCESS_POLICY_ACCESSOR_TYPE_GROUP);

    $this->assertEqual($access_records,
      array(
        '13' => array(
          20 => 1,
        ),
      )
    );
  }

  function testSaveAccessTemplates()
  {
    $db_table = DbTableFactory :: create('SysActionAccessTemplate');
    $items_db_table = DbTableFactory :: create('SysActionAccessTemplateItem');

    //garbage
    $db_table->insert(array('id'=> 1, 'action_name' => 'edit', 'behaviour_id' => 11, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 2, 'action_name' => 'create', 'behaviour_id' => 11, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_GROUP));

    $template = array(
        'create' => array(
            200 => 1,
            210 => 0,
        ),
        'publish' => array(
            200 => 1,
            210 => 1,
        )
    );

    $this->ac->saveAccessTemplates($behaviour_id = 11, $template, ACCESS_POLICY_ACCESSOR_TYPE_USER);

    $templates_rows = $db_table->getList('', 'id', null);
    $items_rows = $items_db_table->getList('', 'id', null);

    $this->assertTrue(is_array($templates_rows));
    $this->assertEqual(count($templates_rows), 3);

    $this->assertTrue(is_array($items_rows));
    $this->assertEqual(count($items_rows), 3);

    $this->assertEqual($templates_rows,
      array(
        array('id' => $templates_rows[0]['id'], 'action_name' => 'create', 'behaviour_id' => 11, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_GROUP),
        array('id' => $templates_rows[1]['id'], 'action_name' => 'create', 'behaviour_id' => 11, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER),
        array('id' => $templates_rows[2]['id'], 'action_name' => 'publish', 'behaviour_id' => 11, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER),
      )
    );

    $record = reset($items_rows);
    $this->assertEqual($record['accessor_id'], 200);
    $this->assertEqual($record['access'], 1);

    $record = end($items_rows);
    $this->assertEqual($record['accessor_id'], 210);
    $this->assertEqual($record['access'], 1);
  }

  function testSaveActionsAccess()
  {
    $db_table = DbTableFactory :: create('SysActionAccess');

    //garbage
    $db_table->insert(array('id'=> 1, 'behaviour_id' => 11, 'action_name' => 'create', 'accessor_id' => 10, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 2, 'behaviour_id' => 11, 'action_name' => 'edit', 'accessor_id' => 20, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_GROUP));

    $access = array(
      10 => array(
        'edit' => 1,
        'publish' => 1,
      ),
      11 => array(
        'edit' => 1,
        'publish' => 0,
      ),
    );

    $this->ac->saveActionsAccess(11, $access, ACCESS_POLICY_ACCESSOR_TYPE_USER);

    $access_rows = $db_table->getList('', 'id', null);
    $this->assertEqual($access_rows,
      array(
        array('id' => $access_rows[0]['id'], 'accessor_id' => 20, 'action_name' => 'edit', 'behaviour_id' => 11, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_GROUP),
        array('id' => $access_rows[1]['id'], 'accessor_id' => 10, 'action_name' => 'edit', 'behaviour_id' => 11, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER),
        array('id' => $access_rows[2]['id'], 'accessor_id' => 10, 'action_name' => 'publish', 'behaviour_id' => 11, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER),
        array('id' => $access_rows[3]['id'], 'accessor_id' => 11, 'action_name' => 'edit', 'behaviour_id' => 11, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER),
      )
    );
  }

  function testSaveObjectsAccess()
  {
    $db_table = DbTableFactory :: create('SysActionAccess');

    //garbage
    $db_table->insert(array('id'=> 1, 'object_id' => 10, 'access' => 1, 'accessor_id' => 100, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 2, 'object_id' => 11, 'access' => 1, 'accessor_id' => 110, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 3, 'object_id' => 10, 'access' => 1, 'accessor_id' => 200, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_GROUP));

    $access = array(
      10 => array(
        100 => 1,
        110 => 1,
      ),
      11 => array(
        100 => 1,
        110 => 0,
      ),
    );

    $this->ac->saveObjectsAccess($access, ACCESS_POLICY_ACCESSOR_TYPE_USER, array(100, 110));

    $access_rows = $db_table->getList('', 'id', null);
    $this->assertEqual($access_rows,
      array(
        array('id' => $access_rows[0]['id'], 'object_id' => 10, 'access' => 1, 'accessor_id' => 200, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_GROUP),
        array('id' => $access_rows[1]['id'], 'object_id' => 10, 'access' => 1, 'accessor_id' => 100, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER),
        array('id' => $access_rows[2]['id'], 'object_id' => 10, 'access' => 1, 'accessor_id' => 110, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER),
        array('id' => $access_rows[3]['id'], 'object_id' => 11, 'access' => 1, 'accessor_id' => 100, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER),
      )
    );
  }

  function testSaveObjectAccessNoAccessorIdsLimit()
  {
    $db_table = DbTableFactory :: create('SysActionAccess');

    //garbage
    $db_table->insert(array('id'=> 1, 'object_id' => 10, 'access' => 1, 'accessor_id' => 100, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 2, 'object_id' => 11, 'access' => 1, 'accessor_id' => 110, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER));
    $db_table->insert(array('id'=> 3, 'object_id' => 10, 'access' => 1, 'accessor_id' => 200, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER));

    $access = array(
      10 => array(
        100 => 1,
        110 => 1,
      ),
      11 => array(
        100 => 1,
        110 => 0,
      ),
    );

    $this->ac->saveObjectsAccess($access, ACCESS_POLICY_ACCESSOR_TYPE_USER);

    $access_rows = $db_table->getList('', 'id', null);
    $this->assertEqual($access_rows,
      array(
        array('id' => $access_rows[0]['id'], 'object_id' => 10, 'access' => 1, 'accessor_id' => 100, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER),
        array('id' => $access_rows[1]['id'], 'object_id' => 10, 'access' => 1, 'accessor_id' => 110, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER),
        array('id' => $access_rows[2]['id'], 'object_id' => 11, 'access' => 1, 'accessor_id' => 100, 'accessor_type' => ACCESS_POLICY_ACCESSOR_TYPE_USER),
      )
    );
  }
}
?>