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
require_once(LIMB_DIR . '/class/lib/util/Ini.class.php');

Mock :: generatePartial(
  'Ini',
  'IniMockVersionOverride',
  array('_parse', '_saveCache')
);

class IniOverrideTest extends LimbTestCase
{
  function setUp()
  {
    DebugMock :: init($this);
  }

  function tearDown()
  {
    DebugMock :: tally();
    clearTestingIni();
  }

  function testOverrideGroupValuesProperly()
  {
    registerTestingIni(
      'testing2.ini',
      '
        [Templates]
        conf = 1
        force_compile = 0
        path = design/templates/      '
    );

    registerTestingIni(
      'testing2.ini.override',
      '
        [Templates]
        conf =
        force_compile = 1
      '
    );

    $ini = new Ini (VAR_DIR . 'testing2.ini', false);

    $this->assertEqual($ini->getOption('conf', 'Templates'), null);
    $this->assertEqual($ini->getOption('path', 'Templates'), 'design/templates/');
    $this->assertEqual($ini->getOption('force_compile', 'Templates'), 1);
  }

  function testOverrideUseRealFile()
  {
    $ini = new Ini(LIMB_DIR . '/tests/cases/util/ini_test2.ini', false);

    $this->assertTrue($ini->hasGroup('test1'));
    $this->assertTrue($ini->hasGroup('test2'));

    $this->assertEqual($ini->getOption('v1', 'test1'), 1);
    $this->assertEqual($ini->getOption('v2', 'test1'), 2);
    $this->assertEqual($ini->getOption('v3', 'test1'), 3);
    $this->assertEqual($ini->getOption('v1', 'test2'), 1);
  }

  function testCacheOriginalFileWasModified()
  {
    registerTestingIni(
      'testing2.ini',
      'test = 1'
    );

    registerTestingIni(
      'testing2.ini.override',
      'test = 2'
    );

    $ini = new Ini(VAR_DIR . 'testing2.ini', true); //ini should be cached here...

    // caching happens very quickly we have to tweak the original file modification time
    // in order to test
    touch($ini->getOriginalFile(), time()+100);
    touch($ini->getOverrideFile(), time()-100);

    $ini_mock = new IniMockVersionOverride($this);
    $ini_mock->expectOnce('_parse');
    $ini_mock->expectOnce('_saveCache');

    $ini_mock->Ini(VAR_DIR . 'testing2.ini', true);

    $ini_mock->tally();

    $ini->resetCache();
  }

  function testCacheOverrideFileWasRemoved()
  {
    registerTestingIni(
      'testing2.ini',
      'test = 1'
    );

    registerTestingIni(
      'testing2.ini.override',
      'test = 2'
    );

    $ini = new Ini(VAR_DIR . 'testing2.ini', true); //ini should be cached here...

    touch($ini->getOriginalFile(), time()-100);
    unlink($ini->getOverrideFile());

    $ini_mock = new IniMockVersionOverride($this);
    $ini_mock->expectOnce('_parse');
    $ini_mock->expectOnce('_saveCache');

    $ini_mock->Ini(VAR_DIR . 'testing2.ini', true);

    $ini_mock->tally();

    $ini->resetCache();
  }

  function testCacheOverrideFileWasModified()
  {
    registerTestingIni(
      'testing2.ini',
      'test = 1'
    );

    registerTestingIni(
      'testing2.ini.override',
      'test = 2'
    );

    $ini = new Ini(VAR_DIR . 'testing2.ini', true); //ini should be cached here...

    // caching happens very quickly we have to tweak the original file modification time
    // in order to test
    touch($ini->getOriginalFile(), time()-100);
    touch($ini->getOverrideFile(), time()+100);

    $ini_mock = new IniMockVersionOverride($this);
    $ini_mock->expectOnce('_parse');
    $ini_mock->expectOnce('_saveCache');

    $ini_mock->Ini(VAR_DIR . 'testing2.ini', true);

    $ini_mock->tally();

    $ini->resetCache();
  }

  function testCacheHit()
  {
    registerTestingIni(
      'testing2.ini',
      'test = 1'
    );

    registerTestingIni(
      'testing2.ini.override',
      'test = 2'
    );

    $ini = new Ini(VAR_DIR . 'testing2.ini', true); //ini should be cached here...

    $ini_mock = new IniMockVersionOverride($this);

    touch($ini->getCacheFile(), time()+100);

    $ini_mock->expectNever('_parse');
    $ini_mock->expectNever('_saveCache');

    $ini_mock->Ini(VAR_DIR . 'testing2.ini', true);

    $ini_mock->tally();

    $ini->resetCache();
  }
}

?>