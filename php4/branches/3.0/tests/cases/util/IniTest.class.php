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
require_once(LIMB_DIR . '/core/util/Ini.class.php');

Mock :: generatePartial(
  'Ini',
  'IniMockVersion',
  array('_parse', '_saveCache')
);

class IniTest extends LimbTestCase
{
  function IniTest()
  {
    parent :: LimbTestCase('ini test');
  }

  function setUp()
  {
    DebugMock :: init($this);
  }

  function tearDown()
  {
    DebugMock :: tally();
    clearTestingIni();
  }

  function testFilePath()
  {
    $ini = new Ini(LIMB_DIR . '/tests/cases/util/ini_test.ini', false);
    $this->assertEqual($ini->getOriginalFile(), LIMB_DIR . '/tests/cases/util/ini_test.ini');
  }

  function testDefaultCharset()
  {
    registerTestingIni(
      'testing.ini',
      ''
    );

    $ini = getIni('testing.ini');
    $this->assertEqual($ini->getCharset(), 'utf8');
  }

  function testCharset()
  {
    registerTestingIni(
      'testing.ini',
      '#charset = iso-8859-1'
    );

    $ini = getIni('testing.ini');
    $this->assertEqual($ini->getCharset(), 'iso-8859-1');
  }

  function testCharset2()
  {
    registerTestingIni(
      'testing.ini',
      '#charset=iso-8859-1 '
    );

    $ini = getIni('testing.ini');
    $this->assertEqual($ini->getCharset(), 'iso-8859-1');
  }

  function testTrimmingFileContents()
  {
    registerTestingIni(
      'testing.ini',
      '
        [group1]
         value = test1
      [group2]
              value = test2
      '
    );

    $ini = getIni('testing.ini');

    $this->assertEqual($ini->getAll(),
      array(
        'group1' => array('value' => 'test1'),
        'group2' => array('value' => 'test2'),
      )
    );
  }

  function testProperComments()
  {
    registerTestingIni(
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

    $ini = getIni('testing.ini');

    $this->assertEqual($ini->getAll(),
      array(
        'group1' => array(
          'value1' => 'test1',
          'value2' => 'test2',
          'value3' => '#'),
      )
    );
  }

  function testStringsWithSpaces()
  {
    registerTestingIni(
      'testing.ini',
      '
      [group1]
       value1 = this is a string with spaces            indeed
       value2 =       "this is string with spaces too
      '
    );

    $ini = getIni('testing.ini');

    $this->assertEqual($ini->getAll(),
      array(
        'group1' => array(
          'value1' => 'this is a string with spaces            indeed',
          'value2' => '"this is string with spaces too',
          ),
      )
    );
  }

  function testProperQuotes()
  {
    registerTestingIni(
      'testing.ini',
      '
      [group1]
       value1 = "  this is a quoted string  "
       value2 = "  this is a quoted string "too"  "
       value3 = "  this is a quoted string \'too\'  "
      '
    );

    $ini = getIni('testing.ini');

    $this->assertEqual($ini->getAll(),
      array(
        'group1' => array(
          'value1' => '  this is a quoted string  ',
          'value2' => '  this is a quoted string "too"  ',
          'value3' => '  this is a quoted string \'too\'  ',
          ),
      )
    );
  }

  function testDefaultGroupExistsOnlyIfGlobalValues()
  {
    registerTestingIni(
      'testing.ini',
      '
      [group1]
       value = test
      '
    );

    $ini = getIni('testing.ini');

    $this->assertFalse($ini->hasGroup('default'));
  }

  function testGlobalValuesInDefaultGroup()
  {
    registerTestingIni(
      'testing.ini',
      '
      value = global_test
      [group1]
       value = test
      '
    );

    $ini = getIni('testing.ini');

    $this->assertEqual($ini->getAll(),
      array(
        'default' => array('value' => 'global_test'),
        'group1' => array('value' => 'test'),
      )
    );

    $this->assertTrue($ini->hasGroup('default'));
  }

  function testNullElements()
  {
    registerTestingIni(
      'testing.ini',
      '
      [group1]
       value =
      '
    );

    $ini = getIni('testing.ini');

    $this->assertEqual($ini->getAll(),
      array('group1' => array('value' => null))
    );

    $this->assertFalse($ini->hasOption('group1', 'value'));
  }

  function testArrayElements()
  {
    registerTestingIni(
      'testing.ini',
      '
      [group1]
       value[] =
       value[] = 1
       value[] =
       value[] = 2
      '
    );

    $ini = getIni('testing.ini');

    $this->assertEqual($ini->getAll(),
      array('group1' => array('value' => array(null, 1, null, 2)))
    );
  }

  function testHashedArrayElements()
  {
    registerTestingIni(
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

    $ini = getIni('testing.ini');

    $this->assertEqual($ini->getAll(),
      array('group1' => array('value' =>
        array('apple' => null, 'banana' => 1, 'fruit' => null)))
    );
  }

  function testCheckers()
  {
    registerTestingIni(
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

    $ini = getIni('testing.ini');

    $this->assertFalse($ini->hasGroup(''));
    $this->assertTrue($ini->hasGroup('default'));
    $this->assertTrue($ini->hasGroup('test'));
    $this->assertTrue($ini->hasGroup('test2'));
    $this->assertTrue($ini->hasGroup('empty_group'));

    $this->assertFalse($ini->hasOption(null, null));
    $this->assertFalse($ini->hasOption('', ''));
    $this->assertFalse($ini->hasOption('', 'no_such_block'));
    $this->assertTrue($ini->hasOption('test', 'test'));
    $this->assertFalse($ini->hasOption('no_such_variable', 'test3'));
    $this->assertTrue($ini->hasOption('unassigned', 'default'));
    $this->assertTrue($ini->hasOption('test', 'default'));
  }

  function testGetOption()
  {
    registerTestingIni(
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

    $ini = getIni('testing.ini');

    $this->assertEqual($ini->getOption('unassigned'), '');
    $this->assertEqual($ini->getOption('test'), 1);

    DebugMock :: expectWriteNotice('undefined option',
      array(
        'ini' => $ini->getOriginalFile(),
        'group' => 'default',
        'option' => 'no_such_option'
      )
    );

    $this->assertEqual($ini->getOption('no_such_option'), '');

    DebugMock :: expectWriteNotice('undefined group',
      array(
        'ini' => $ini->getOriginalFile(),
        'group' => 'no_such_group',
        'option' => 'test'
      )
    );

    $this->assertEqual($ini->getOption('test', 'no_such_group'), '');

    $this->assertEqual($ini->getOption('test', 'test'), 1);

    $var = $ini->getOption('test', 'test2');
    $this->assertEqual($var, array(1, 2));

    $var = $ini->getOption('test', 'test3');
    $this->assertEqual($var, array('wow' => 1, 'hey' => 2));
  }

  function testReplaceConstants()
  {
    define('INI_TEST_UNIQUE_CONSTANT', '*constant*');

    registerTestingIni(
      'testing.ini',
      '
        [test]
        test = {INI_TEST_UNIQUE_CONSTANT}1
      '
    );

    $ini = getIni('testing.ini');

    $this->assertEqual($ini->getOption('test', 'test'), '*constant*1');
  }

  function testGetGroup()
  {
    registerTestingIni(
      'testing.ini',
      '
        unassigned =
        test = 1

        [test]
        test = 1
      '
    );

    $ini = getIni('testing.ini');

    $this->assertEqual($ini->getGroup('default'), array('unassigned' => '', 'test' => 1));
    $this->assertEqual($ini->getGroup('test'), array('test' => 1));

    DebugMock :: expectWriteNotice('undefined group',
      array(
        'ini' => $ini->getOriginalFile(),
        'group' => 'no_such_group'
      )
    );

    $this->assertNull($ini->getGroup('no_such_group'));
  }

  function testAssignOption()
  {
    registerTestingIni(
      'testing.ini',
      '
        unassigned =
        test = 1

        [test]
        test = 2
      '
    );

    $ini = getIni('testing.ini');

    $this->assertTrue($ini->assignOption($test, 'unassigned'));
    $this->assertEqual($test, '');

    $this->assertTrue($ini->assignOption($test, 'test'));
    $this->assertEqual($test, 1);

    $this->assertTrue($ini->assignOption($test, 'test', 'test'));
    $this->assertEqual($test, 2);
    $this->assertFalse($ini->assignOption($test, 'no_such_option', 'test'));
    $this->assertEqual($test, 2);
  }

  function testCacheHit()
  {
    registerTestingIni(
      'testing.ini',
      'test = 1'
    );

    $ini = new Ini(VAR_DIR . 'testing.ini', true); //ini should be cached here...

    // caching happens very quickly we have to tweak the cache file modification time
    // in order to test cache hit
    touch($ini->getCacheFile(), time()+100);

    $ini_mock = new IniMockVersion($this);
    $ini_mock->expectNever('_parse');
    $ini_mock->expectNever('_saveCache');

    $ini_mock->Ini(VAR_DIR . 'testing.ini', true);

    $ini_mock->tally();

    $this->assertEqual($ini->getAll(), $ini_mock->getAll());

    $ini->resetCache();
  }

  function testNoCache()
  {
    registerTestingIni(
      'testing.ini',
      'test = 1'
    );

    $ini = new Ini(VAR_DIR . 'testing.ini', true); //ini should be cached here...

    $this->assertTrue(file_exists($ini->getCacheFile()));
    unlink($ini->getCacheFile());

    $ini_mock = new IniMockVersion($this);
    $ini_mock->expectOnce('_parse');
    $ini_mock->expectOnce('_saveCache');

    $ini_mock->Ini(VAR_DIR . 'testing.ini', true);

    $ini_mock->tally();

    $ini->resetCache();
  }

  function testCacheHitFileWasModified()
  {
    registerTestingIni(
      'testing.ini',
      'test = 1'
    );

    $ini = new Ini(VAR_DIR . 'testing.ini', true); //ini should be cached here...

    // caching happens very quickly we have to tweak the original file modification time
    // in order to test
    touch($ini->getOriginalFile(), time()+100);

    $ini_mock = new IniMockVersion($this);
    $ini_mock->expectOnce('_parse');
    $ini_mock->expectOnce('_saveCache');

    $ini_mock->Ini(VAR_DIR . 'testing.ini', true);

    $ini_mock->tally();

    $ini->resetCache();
  }

  function testCacheSave()
  {
    registerTestingIni(
      'testing.ini',
      'test = 1'
    );

    $ini = new IniMockVersion($this);
    $ini->expectOnce('_parse');
    $ini->expectOnce('_saveCache');

    $ini->Ini(VAR_DIR . 'testing.ini', true);

    $ini->tally();

    $ini->resetCache();
  }

  function testDontCache()
  {
    registerTestingIni(
      'testing.ini',
      'test = 1'
    );

    $ini = new IniMockVersion($this);

    $ini->expectOnce('_parse');
    $ini->expectNever('_saveCache');

    $ini->Ini(VAR_DIR . 'testing.ini', false);

    $ini->tally();
  }

  function testParseRealFile()
  {
    $ini = new Ini(LIMB_DIR . '/tests/cases/util/ini_test.ini', false);
    $this->assertEqual($ini->getAll(), array('test' => array('test' => 1)));
  }
}

?>