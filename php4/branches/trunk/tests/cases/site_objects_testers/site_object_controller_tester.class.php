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
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/core/model/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/core/model/site_object_factory.class.php');
require_once(LIMB_DIR . '/core/actions/empty_action.class.php');

class site_object_controller_tester extends UnitTestCase
{
  var $db = null;
  var $class_name = '';
  var $object = null;

  function site_object_controller_tester($class_name)
  {
    $this->db =& db_factory :: instance();

    $this->class_name = $class_name;

    parent :: UnitTestCase();
  }

  function &_create_site_object()
  {
    return site_object_factory :: create($this->class_name);
  }

  function setUp()
  {
    $this->object = $this->_create_site_object();

    $this->_clean_up();

    debug_mock :: init($this);
  }

  function tearDown()
  {
    $this->_clean_up();

    debug_mock :: tally();
  }

  function _clean_up()
  {
    $this->db->sql_delete('sys_class');
  }

  function test_class_properties()
  {
    $props = $this->object->get_class_properties();

    if(isset($props['abstract_class']) && $props['abstract_class'])
      return;

    $this->_do_test_class_properties($props);
  }

  function _do_test_class_properties($props)
  {
    if(isset($props['controller_class_name']))
    {
      $this->assertEqual(get_class($this->object->get_controller()), $props['controller_class_name']);
    }
  }

  function test_controller()
  {
    $controller = $this->object->get_controller();

    if(is_a($controller, 'empty_controller'))
      return;

    $definitions = $controller->get_actions_definitions();
    $controller_class = get_class($controller);

    $empty_action = new empty_action();

    foreach($definitions as $action => $data)
    {
      if (isset($data['template_path']))
      {
        $template = new template($data['template_path']);

        $this->_check_template($template);
      }

      if(isset($data['action_path']))
      {
        debug_mock :: expect_never_write('write_error');

        $action_obj = action_factory :: create($data['action_path']);

        $this->assertNotIdentical($action_obj, $empty_action,
          'controller: "' . $controller_class .
          '" action object for action "' . $action . '"not found');

        $this->_check_action($action_obj);
      }

      if(isset($data['action_name']))
      {
        $this->assertTrue(($data['action_name']),
          'controller: "' . $controller_class .
          '" action_name property for action "' . $action . '" is empty - check strings');
      }
    }

    $action = $controller->get_default_action();

    $this->assertTrue(isset($definitions[$action]),
      'controller: "' . $controller_class .
      '" default action "' . $action . '" doesnt exist');
  }

  function _check_action(&$action)
  {
    if(!is_subclass_of($action, 'form_create_site_object_action') &&
        !is_subclass_of($action, 'form_edit_site_object_action'))
    return;

    $datamap = $action->get_datamap();

    $action->_init_validator(); //this is not a very good idea...
    $validator = $action->get_validator();

    $rules = $validator->get_rules();
    $site_object = $action->get_site_object();

    $attributes_definition = $site_object->get_attributes_definition();

    foreach($datamap as $src_field => $dst_field)
    {
      $this->assertTrue(isset($attributes_definition[$dst_field]),
      'no such field in site_object "' . get_class($site_object) . '" attributes definition "' . $dst_field. '" defined in action "' . get_class($action) . '"' );
    }

    foreach($rules as $rule)
    {
      if(!is_subclass_of($rule, 'single_field_rule'))
        continue;

      $field_name = $rule->get_field_name();

      $this->assertTrue(isset($datamap[$field_name]),
        'no such field in datamap(validator rule) "' . $field_name. '" in "' . get_class($action) . '"' );
    }
  }

  function _check_template(&$template)
  {
  }
}

?>