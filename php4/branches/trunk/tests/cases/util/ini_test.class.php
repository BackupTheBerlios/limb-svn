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
require_once(LIMB_DIR . '/core/lib/util/ini.class.php');

Mock :: generatePartial(
  'ini',
  'ini_mock_version',
  array('_parse', '_save_cache')
);

class ini_test extends LimbTestCase
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

  function test_same_instance()
  {
    $ini =& ini :: instance(LIMB_DIR . '/tests/cases/util/ini_test.ini', false);

    $this->assertIsA($ini, 'ini');

    $ini2 =& ini :: instance(LIMB_DIR . '/tests/cases/util/ini_test.ini', false);

    $this->assertReference($ini, $ini2);
  }

  function test_file_path()
  {
    $ini =& ini :: instance(LIMB_DIR . '/tests/cases/util/ini_test.ini', false);
    $this->assertEqual($ini->get_original_file(), LIMB_DIR . '/tests/cases/util/ini_test.ini');
  }

  function test_default_charset()
  {
    register_testing_ini(
      'testing.ini',
      ''
    );

    $ini =& get_ini('testing.ini');
    $this->assertEqual($ini->get_charset(), 'utf8');
  }

  function test_charset()
  {
    register_testing_ini(
      'testing.ini',
      '#charset = iso-8859-1'
    );

    $ini =& get_ini('testing.ini');
    $this->assertEqual($ini->get_charset(), 'iso-8859-1');
  }

  function test_charset2()
  {
    register_testing_ini(
      'testing.ini',
      '#charset=iso-8859-1 '
    );

    $ini =& get_ini('testing.ini');
    $this->assertEqual($ini->get_charset(), 'iso-8859-1');
  }

  function test_trimming_file_contents()
  {
    register_testing_ini(
      'testing.ini',
      '
        [group1]
         value = test1
      [group2]
              value = test2
      '
    );

    $ini =& get_ini('testing.ini');

    $this->assertEqual($ini->get_all(),
      array(
        'group1' => array('value' => 'test1'),
        'group2' => array('value' => 'test2'),
      )
    );
  }

  function test_proper_comments()
  {
    register_testing_ini(
      'testing.ini',
      '
      #[group_is_commented]
      [group1]
       value1 = test1#this a commentary #too#
       #"this is just a commentary"
       value2 = test2
       value3 = "#" # symbols are allowed inside of ""
      '
    );

    $ini =& get_ini('testing.ini');

    $this->assertEqual($ini->get_all(),
      array(
        'group1' => array(
          'value1' => 'test1',
          'value2' => 'test2',
          'value3' => '#'),
      )
    );
  }

  function test_strings_with_spaces()
  {
    register_testing_ini(
      'testing.ini',
      '
      [group1]
       value1 = this is a string with spaces            indeed
       value2 =       "this is string with spaces too
      '
    );

    $ini =& get_ini('testing.ini');

    $this->assertEqual($ini->get_all(),
      array(
        'group1' => array(
          'value1' => 'this is a string with spaces            indeed',
          'value2' => '"this is string with spaces too',
          ),
      )
    );
  }

  function test_proper_quotes()
  {
    register_testing_ini(
      'testing.ini',
      '
      [group1]
       value1 = "  this is a quoted string  "
       value2 = "  this is a quoted string "too"  "
       value3 = "  this is a quoted string \'too\'  "
      '
    );

    $ini =& get_ini('testing.ini');

    $this->assertEqual($ini->get_all(),
      array(
        'group1' => array(
          'value1' => '  this is a quoted string  ',
          'value2' => '  this is a quoted string "too"  ',
          'value3' => '  this is a quoted string \'too\'  ',
          ),
      )
    );
  }

  function test_default_group_exists_only_if_global_values()
  {
    register_testing_ini(
      'testing.ini',
      '
      [group1]
       value = test
      '
    );

    $ini =& get_ini('testing.ini');

    $this->assertFalse($ini->has_group('default'));
  }

  function test_global_values_in_default_group()
  {
    register_testing_ini(
      'testing.ini',
      '
      value = global_test
      [group1]
       value = test
      '
    );

    $ini =& get_ini('testing.ini');

    $this->assertEqual($ini->get_all(),
      array(
        'default' => array('value' => 'global_test'),
        'group1' => array('value' => 'test'),
      )
    );

    $this->assertTrue($ini->has_group('default'));
  }

  function test_null_elements()
  {
    register_testing_ini(
      'testing.ini',
      '
      [group1]
       value =
      '
    );

    $ini =& get_ini('testing.ini');

    $this->assertEqual($ini->get_all(),
      array('group1' => array('value' => null))
    );

    $this->assertFalse($ini->has_option('group1', 'value'));
  }

  function test_array_elements()
  {
    register_testing_ini(
      'testing.ini',
      '
      [group1]
       value[] =
       value[] = 1
       value[] =
       value[] = 2
      '
    );

    $ini =& get_ini('testing.ini');

    $this->assertEqual($ini->get_all(),
      array('group1' => array('value' => array(null, 1, null, 2)))
    );
  }

  function test_hashed_array_elements()
  {
    register_testing_ini(
      'testing.ini',
      '
      [group1]
       value[apple] =
       value[banana] = 1
       value[fruit] =
       value["lime"] = not valid index!
       value[\'lime\'] = not valid index too!
      '
    );

    $ini =& get_ini('testing.ini');

    $this->assertEqual($ini->get_all(),
      array('group1' => array('value' =>
        array('apple' => null, 'banana' => 1, 'fruit' => null)))
    );
  }

  function test_checkers()
  {
    register_testing_ini(
      'testing.ini',
      '
        unassigned =
        test = 1

        [test]
        test = 1

        [test2]
        test3 =

        [empty_group]
        test = '
    );

    $ini =& get_ini('testing.ini');

    $this->assertFalse($ini->has_group(''));
    $this->assertTrue($ini->has_group('default'));
    $this->assertTrue($ini->has_group('test'));
    $this->assertTrue($ini->has_group('test2'));
    $this->assertTrue($ini->has_group('empty_group'));

    $this->assertFalse($ini->has_option(null, null));
    $this->assertFalse($ini->has_option('', ''));
    $this->assertFalse($ini->has_option('', 'no_such_block'));
    $this->assertTrue($ini->has_option('test', 'test'));
    $this->assertFalse($ini->has_option('no_such_variable', 'test3'));
    $this->assertTrue($ini->has_option('unassigned', 'default'));
    $this->assertTrue($ini->has_option('test', 'default'));
  }

  function test_get_option()
  {
    register_testing_ini(
      'testing.ini',
      '
        unassigned =
        test = 1

        [test]
        test = 1

        [test2]
        test[] = 1
        test[] = 2

        [test3]
        test[wow] = 1
        test[hey] = 2'
    );

    $ini =& get_ini('testing.ini');

    $this->assertEqual($ini->get_option('unassigned'), '');
    $this->assertEqual($ini->get_option('test'), 1);

    debug_mock :: expect_write_notice('undefined option',
      array(
        'ini' => $ini->get_original_file(),
        'group' => 'default',
        'option' => 'no_such_option'
      )
    );

    $this->assertEqual($ini->get_option('no_such_option'), '');

    debug_mock :: expect_write_notice('undefined group',
      array(
        'ini' => $ini->get_original_file(),
        'group' => 'no_such_group',
        'option' => 'test'
      )
    );

    $this->assertEqual($ini->get_option('test', 'no_such_group'), '');

    $this->assertEqual($ini->get_option('test', 'test'), 1);

    $var = $ini->get_option('test', 'test2');
    $this->assertEqual($var, array(1, 2));

    $var = $ini->get_option('test', 'test3');
    $this->assertEqual($var, array('wow' => 1, 'hey' => 2));
  }

  function test_get_group()
  {
    register_testing_ini(
      'testing.ini',
      '
        unassigned =
        test = 1

        [test]
        test = 1
      '
    );

    $ini =& get_ini('testing.ini');

    $this->assertEqual($ini->get_group('default'), array('unassigned' => '', 'test' => 1));
    $this->assertEqual($ini->get_group('test'), array('test' => 1));

    debug_mock :: expect_write_notice('undefined group',
      array(
        'ini' => $ini->get_original_file(),
        'group' => 'no_such_group'
      )
    );

    $this->assertNull($ini->get_group('no_such_group'));
  }

  function test_assign_option()
  {
    register_testing_ini(
      'testing.ini',
      '
        unassigned =
        test = 1

        [test]
        test = 2
      '
    );

    $ini =& get_ini('testing.ini');

    $this->assertTrue($ini->assign_option($test, 'unassigned'));
    $this->assertEqual($test, '');

    $this->assertTrue($ini->assign_option($test, 'test'));
    $this->assertEqual($test, 1);

    $this->assertTrue($ini->assign_option($test, 'test', 'test'));
    $this->assertEqual($test, 2);
    $this->assertFalse($ini->assign_option($test, 'no_such_option', 'test'));
    $this->assertEqual($test, 2);
  }

  function test_cache_hit()
  {
    register_testing_ini(
      'testing.ini',
      'test = 1'
    );

    $ini =& new ini(VAR_DIR . 'testing.ini', true); //ini should be cached here...

    // caching happens very quickly we have to tweak the cache file modification time
    // in order to test cache hit
    touch($ini->get_cache_file(), time()+100);

    $ini_mock =& new ini_mock_version($this);
    $ini_mock->expectNever('_parse');
    $ini_mock->expectNever('_save_cache');

    $ini_mock->ini(VAR_DIR . 'testing.ini', true);

    $ini_mock->tally();

    $this->assertEqual($ini->get_all(), $ini_mock->get_all());

    $ini->reset_cache();
  }

  function test_no_cache()
  {
    register_testing_ini(
      'testing.ini',
      'test = 1'
    );

    $ini =& new ini(VAR_DIR . 'testing.ini', true); //ini should be cached here...

    $this->assertTrue(file_exists($ini->get_cache_file()));
    unlink($ini->get_cache_file());

    $ini_mock =& new ini_mock_version($this);
    $ini_mock->expectOnce('_parse');
    $ini_mock->expectOnce('_save_cache');

    $ini_mock->ini(VAR_DIR . 'testing.ini', true);

    $ini_mock->tally();

    $ini->reset_cache();
  }

  function test_cache_hit_file_was_modified()
  {
    register_testing_ini(
      'testing.ini',
      'test = 1'
    );

    $ini =& new ini(VAR_DIR . 'testing.ini', true); //ini should be cached here...

    // caching happens very quickly we have to tweak the original file modification time
    // in order to test
    touch($ini->get_original_file(), time()+100);

    $ini_mock =& new ini_mock_version($this);
    $ini_mock->expectOnce('_parse');
    $ini_mock->expectOnce('_save_cache');

    $ini_mock->ini(VAR_DIR . 'testing.ini', true);

    $ini_mock->tally();

    $ini->reset_cache();
  }

  function test_cache_save()
  {
    register_testing_ini(
      'testing.ini',
      'test = 1'
    );

    $ini =& new ini_mock_version($this);
    $ini->expectOnce('_parse');
    $ini->expectOnce('_save_cache');

    $ini->ini(VAR_DIR . 'testing.ini', true);

    $ini->tally();

    $ini->reset_cache();
  }

  function test_dont_cache()
  {
    register_testing_ini(
      'testing.ini',
      'test = 1'
    );

    $ini =& new ini_mock_version($this);

    $ini->expectOnce('_parse');
    $ini->expectNever('_save_cache');

    $ini->ini(VAR_DIR . 'testing.ini', false);

    $ini->tally();
  }

  function test_parse_real_file()
  {
    $ini =& ini :: instance(LIMB_DIR . '/tests/cases/util/ini_test.ini', false);
    $this->assertEqual($ini->get_all(), array('test' => array('test' => 1)));
  }
}

?>