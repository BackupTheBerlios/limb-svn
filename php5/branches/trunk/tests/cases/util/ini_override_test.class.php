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
require_once(LIMB_DIR . '/class/lib/util/ini.class.php');

Mock :: generatePartial(
  'ini',
  'ini_mock_version_override',
  array('_parse', '_save_cache')
);

class ini_override_test extends LimbTestCase
{
  function setUp()
  {
    debug_mock :: init($this);
  }

  function tearDown()
  {
    debug_mock :: tally();
    clear_testing_ini();
  }

  function test_override_group_values_properly()
  {
    register_testing_ini(
      'testing2.ini',
      '
        [Templates]
        conf = 1
        force_compile = 0
        path = design/templates/      '
    );

    register_testing_ini(
      'testing2.ini.override',
      '
        [Templates]
        conf =
        force_compile = 1
      '
    );

    $ini = new ini (VAR_DIR . 'testing2.ini', false);

    $this->assertEqual($ini->get_option('conf', 'Templates'), null);
    $this->assertEqual($ini->get_option('path', 'Templates'), 'design/templates/');
    $this->assertEqual($ini->get_option('force_compile', 'Templates'), 1);
  }

  function test_override_use_real_file()
  {
    $ini = new ini(LIMB_DIR . '/tests/cases/util/ini_test2.ini', false);

    $this->assertTrue($ini->has_group('test1'));
    $this->assertTrue($ini->has_group('test2'));

    $this->assertEqual($ini->get_option('v1', 'test1'), 1);
    $this->assertEqual($ini->get_option('v2', 'test1'), 2);
    $this->assertEqual($ini->get_option('v3', 'test1'), 3);
    $this->assertEqual($ini->get_option('v1', 'test2'), 1);
  }

  function test_cache_original_file_was_modified()
  {
    register_testing_ini(
      'testing2.ini',
      'test = 1'
    );

    register_testing_ini(
      'testing2.ini.override',
      'test = 2'
    );

    $ini = new ini(VAR_DIR . 'testing2.ini', true); //ini should be cached here...

    // caching happens very quickly we have to tweak the original file modification time
    // in order to test
    touch($ini->get_original_file(), time()+100);
    touch($ini->get_override_file(), time()-100);

    $ini_mock = new ini_mock_version_override($this);
    $ini_mock->expectOnce('_parse');
    $ini_mock->expectOnce('_save_cache');

    $ini_mock->__construct(VAR_DIR . 'testing2.ini', true);

    $ini_mock->tally();

    $ini->reset_cache();
  }

  function test_cache_override_file_was_removed()
  {
    register_testing_ini(
      'testing2.ini',
      'test = 1'
    );

    register_testing_ini(
      'testing2.ini.override',
      'test = 2'
    );

    $ini = new ini(VAR_DIR . 'testing2.ini', true); //ini should be cached here...

    touch($ini->get_original_file(), time()-100);
    unlink($ini->get_override_file());

    $ini_mock = new ini_mock_version_override($this);
    $ini_mock->expectOnce('_parse');
    $ini_mock->expectOnce('_save_cache');

    $ini_mock->__construct(VAR_DIR . 'testing2.ini', true);

    $ini_mock->tally();

    $ini->reset_cache();
  }

  function test_cache_override_file_was_modified()
  {
    register_testing_ini(
      'testing2.ini',
      'test = 1'
    );

    register_testing_ini(
      'testing2.ini.override',
      'test = 2'
    );

    $ini = new ini(VAR_DIR . 'testing2.ini', true); //ini should be cached here...

    // caching happens very quickly we have to tweak the original file modification time
    // in order to test
    touch($ini->get_original_file(), time()-100);
    touch($ini->get_override_file(), time()+100);

    $ini_mock = new ini_mock_version_override($this);
    $ini_mock->expectOnce('_parse');
    $ini_mock->expectOnce('_save_cache');

    $ini_mock->__construct(VAR_DIR . 'testing2.ini', true);

    $ini_mock->tally();

    $ini->reset_cache();
  }

  function test_cache_hit()
  {
    register_testing_ini(
      'testing2.ini',
      'test = 1'
    );

    register_testing_ini(
      'testing2.ini.override',
      'test = 2'
    );

    $ini = new ini(VAR_DIR . 'testing2.ini', true); //ini should be cached here...

    $ini_mock = new ini_mock_version_override($this);

    touch($ini->get_cache_file(), time()+100);

    $ini_mock->expectNever('_parse');
    $ini_mock->expectNever('_save_cache');

    $ini_mock->__construct(VAR_DIR . 'testing2.ini', true);

    $ini_mock->tally();

    $ini->reset_cache();
  }
}

?>