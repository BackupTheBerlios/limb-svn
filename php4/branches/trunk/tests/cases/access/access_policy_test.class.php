<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/tests/cases/db_test.class.php');

require_once(LIMB_DIR . '/core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . '/core/model/access_policy.class.php');

Mock::generatePartial
(
  'access_policy',
  'access_policy_test_version',
  array('_get_controller')
);

Mock::generate('site_object_controller');

class access_policy_test extends db_test
{
  var $dump_file = 'access_policy_load.sql';

  var $ac = null;

  var $objects_to_filter = array();

  var $objects_to_assign_actions  = array();

  var $site_object_controller_actions = array();

  function setUp()
  {
    parent :: setUp();

    $this->ac = new access_policy_test_version($this);

    $this->objects_to_assign_actions = array(
      1 => array(
        'id' => 300,
        'controller_id' => 10,
        'controller_name' => 'site_object_access_test',
      ),
      2 => array(
        'id' => 302,
        'controller_id' => 10,
        'controller_name' => 'site_object_access_test',
      ),
      3 => array(
        'id' => 303,
        'controller_id' => 10,
        'controller_name' => 'site_object_access_test',
      )
    );

    $this->objects_to_filter = array(300, 300, 301, 302, 303);

    $this->site_object_controller_actions = array(
        'display' => array(),
        'create' => array(),
        'edit' => array(),
        'publish' => array(),
        'delete' => array(),
    );
  }

  function tearDown()
  {
    parent :: tearDown();

    $user =& user :: instance();
    $user->logout();

    $this->ac->tally();
  }

  function _login_user($id, $groups)
  {
    $user =& user :: instance();

    $user->_set_id($id);
    $user->_set_groups($groups);
  }

  function test_get_accessible_objects()
  {
    $this->_login_user(200, array(100 => 'admins'));

    $object_ids = $this->ac->get_accessible_objects($this->objects_to_filter);

    $this->assertEqual(sizeof($object_ids), 3);

    $this->_login_user(210, array(110 => 'visitors'));

    $object_ids = $this->ac->get_accessible_objects($this->objects_to_filter);

    $this->assertEqual(sizeof($object_ids), 4);
  }

  function test_assign_actions()
  {
    $this->_login_user(200, array(100 => 'admins'));

    $m =& new Mocksite_object_controller($this);
    $m->setReturnValue('get_actions_definitions', $this->site_object_controller_actions);

    $this->ac->expectOnce('_get_controller');
    $this->ac->setReturnReference('_get_controller', $m, array('site_object_access_test'));

    $this->ac->assign_actions_to_objects($this->objects_to_assign_actions);

    $m->tally();

    $obj = $this->objects_to_assign_actions[1];
    $this->assertEqual(sizeof($obj['actions']), 4);

    $this->assertEqual($obj['actions'],
      array(
        'create' => array(),
        'display' => array(),
        'edit' => array(),
        'delete' => array(),
      )
    );
  }
}

?>