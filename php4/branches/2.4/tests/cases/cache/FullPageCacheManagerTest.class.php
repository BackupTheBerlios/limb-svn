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
require_once(LIMB_DIR . '/class/cache/FullPageCacheManager.class.php');
require_once(LIMB_DIR . '/class/core/request/Request.class.php');
require_once(LIMB_DIR . '/class/lib/http/Uri.class.php');

Mock :: generate('Request');
Mock :: generate('Uri');

Mock :: generatePartial(
  'FullPageCacheManager',
  'FullPageCacheManagerTestVersion',
  array('getRules', '_setMatchedRule', '_getMatchedRule', '_isUserInGroups')
);

Mock :: generatePartial(
  'FullPageCacheManager',
  'FullPageCacheManagerTestVersion2',
  array('isCacheable', 'cacheExists', 'getCacheId')
);

class FullPageCacheManagerTest extends LimbTestCase
{
  var $cache_manager;
  var $request;
  var $uri;

  function FullPageCacheManagerTest()
  {
    parent :: LimbTestCase('full page cache test');
  }

  function setUp()
  {
    $this->request = new MockRequest($this);
    $this->uri = new MockUri($this);

    $this->request->setReturnReference('getUri', $this->uri);

    $this->cache_manager = new FullPageCacheManagerTestVersion($this);
    $this->cache_manager->setRequest($this->request);
  }

  function tearDown()
  {
    $this->cache_manager->tally();
    $this->request->tally();
    $this->uri->tally();
  }

  function testGetRulesFromIni()
  {
    $cache_manager = new FullPageCacheManager();

    registerTestingIni(
      'full_page_cache.ini',
      '
      [rule1]
       path_regex = /root/test1
       optional[] = test1
       optional[] = test2
      [rule2]
       path_regex = /root/test2
       groups[] = members
       groups[] = visitors
      [not_valid_rule]
       path_regex = /root/test3
      '
    );

    $this->assertEqual($cache_manager->getRules(),
      array(
        array('path_regex' => '/root/test1', 'optional' => array('test1', 'test2')),
        array('path_regex' => '/root/test2', 'groups' => array('members', 'visitors'))
      )
    );

    clearTestingIni();
  }

  function testIsNotCacheableNoUri()
  {
    $cache_manager = new FullPageCacheManager();

    $this->assertFalse($cache_manager->isCacheable());
  }

  function testIsCacheableRequiredAttributes()
  {
    $this->request->setReturnValue('export', array('action' => 1, 'pager' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'required' => array('action', 'pager'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->assertTrue($this->cache_manager->isCacheable());
    $this->cache_manager->expectOnce('_setMatchedRule', array($rule));
  }

  function testIsCacheableOptionalAttributes()
  {
    $this->request->setReturnValue('export', array('action' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'optional' => array('action', 'pager'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->assertTrue($this->cache_manager->isCacheable());
    $this->cache_manager->expectOnce('_setMatchedRule', array($rule));
  }

  function testIsCacheableGroupsMatch()
  {
    $this->request->setReturnValue('export', array('action' => 1, 'pager' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->cache_manager->setReturnValueAt(0, '_isUserInGroups', true, array(array('members', 'visitors')));

    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'optional' => array('action', 'pager'),
               'type' => 'allow',
               'groups' => array('members', 'visitors')
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->assertTrue($this->cache_manager->isCacheable());
    $this->cache_manager->expectOnce('_setMatchedRule', array($rule));
  }

  function testIsNotCacheableGroupsDontMatch()
  {
    $this->request->setReturnValue('export', array('action' => 1, 'pager' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->cache_manager->setReturnValueAt(0, '_isUserInGroups', false, array(array('members')));

    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'optional' => array('action', 'pager'),
               'type' => 'allow',
               'groups' => array('members')
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->assertFalse($this->cache_manager->isCacheable());
    $this->cache_manager->expectNever('_setMatchedRule');
  }

  function testIsCacheableAllowByDefault()
  {
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'optional' => array('action', 'pager'),
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->request->setReturnValue('export', array('action' => 1, 'pager' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->assertTrue($this->cache_manager->isCacheable());
    $this->cache_manager->expectOnce('_setMatchedRule', array($rule));
  }

  function testIsNotCacheableDeny()
  {
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'optional' => array('action', 'pager'),
               'type' => 'deny'
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->request->setReturnValue('export', array('action' => 1, 'pager' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->assertFalse($this->cache_manager->isCacheable());
    $this->cache_manager->expectNever('_setMatchedRule');
  }

  function testIsCacheableExtraQueryItems()
  {
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'required' => array('action', 'pager'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->request->setReturnValue('export', array('action' => 1, 'pager' => 1, 'extra' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->assertTrue($this->cache_manager->isCacheable());
    $this->cache_manager->expectOnce('_setMatchedRule', array($rule));
  }

  function testIsNotCacheableNotEnoughAttributes()
  {
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'required' => array('action', 'pager'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->request->setReturnValue('export', array('action' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->assertFalse($this->cache_manager->isCacheable());
    $this->cache_manager->expectNever('_setMatchedRule');
  }

  function testIsNotCacheableAttributesDontMatch()
  {
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'required' => array('action', 'pager'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->request->setReturnValue('export', array('action' => 1, 'pager1' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->assertFalse($this->cache_manager->isCacheable());
    $this->cache_manager->expectNever('_setMatchedRule');
  }

  function testIsNotCacheablePathDoesntMatch()
  {
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'attributes' => array('action'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->request->setReturnValue('export', array('action' => 1));
    $this->uri->setReturnValue('getPath', '/root/tesx');

    $this->assertFalse($this->cache_manager->isCacheable());
    $this->cache_manager->expectNever('_setMatchedRule');
  }

  function testIsCacheableSecondRuleMatch()
  {
    $rule1 = array(
           'path_regex' => '/^\/root\/test.*$/',
           'optional' => array('action', 'pager'),
           'type' => 'allow'
    );

    $rule2 = array(
           'path_regex' => '/^\/root\/test.*$/',
           'optional' => array('action'),
           'type' => 'allow'
    );

    $rule3 = array(
           'path_regex' => '/^\/root\/test.*$/',
           'type' => 'allow'
    );

    $this->cache_manager->setReturnValue('getRules',
      array($rule1, $rule2, $rule3)
    );

    $this->request->setReturnValue('export', array('action' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->assertTrue($this->cache_manager->isCacheable());
    $this->cache_manager->expectOnce('_setMatchedRule', array($rule2));
  }

  function testIsNotCacheableSecondRuleDeny()
  {
    $rule1 = array(
           'path_regex' => '/^\/root\/test.*$/',
           'required' => array('action', 'pager'),
           'type' => 'allow'
    );

    $rule2 = array(
           'path_regex' => '/^\/root\/test.*$/',
           'required' => array('action'),
           'type' => 'deny'
    );

    $this->cache_manager->setReturnValue('getRules',
      array($rule1, $rule2)
    );

    $this->request->setReturnValue('export', array('action' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->assertFalse($this->cache_manager->isCacheable());
    $this->cache_manager->expectNever('_setMatchedRule');
  }

  function testIsCacheableNoAttributes()
  {
    $rule = array(
           'path_regex' => '/^\/root\/test.*$/',
           'type' => 'allow'
    );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->request->setReturnValue('export', array('action' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->assertTrue($this->cache_manager->isCacheable());
    $this->cache_manager->expectOnce('_setMatchedRule', array($rule));
  }

  function testGetCacheIdNoMatchedRule()
  {
    $this->cache_manager->setReturnValue('_getMatchedRule', null);
    $this->assertNull($this->cache_manager->getCacheId());
  }

  function testGetCacheIdOptionalMergedWithRequired()
  {
    $rule = array('optional' => array('pager', 'prop'), 'required' => array('action'));

    $this->request->setReturnValue('export', $query_items = array('action' => 1, 'pager' => 2));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->cache_manager->setReturnValue('_getMatchedRule', $rule);

    $this->assertEqual(
      $this->cache_manager->getCacheId(),
      'f_' . md5('/root/test' . serialize($query_items))
    );
  }

  function testGetCacheIdQueryItemsSorted()
  {
    $rule = array('optional' => array('pager', 'prop'), 'required' => array('action'));

    $this->request->setReturnValue('export', $query_items = array('action' => 1, 'pager' => 2));

    $this->uri->setReturnValue('getPath', '/root/test');

    $this->cache_manager->setReturnValue('_getMatchedRule', $rule);

    $cache_id = $this->cache_manager->getCacheId();

    $this->request->setReturnValue('export', $query_items = array('junky' => 1, 'pager' => 2, 'action' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->cache_manager->setReturnValue('_getMatchedRule', $rule);

    $this->assertEqual(
      $this->cache_manager->getCacheId(),
      $cache_id
    );
  }

  function testGetCacheIdNoAttributes()
  {
    $rule = array();

    $this->request->setReturnValue('export', $query_items = array('action' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->cache_manager->setReturnValue('_getMatchedRule', $rule);

    $this->assertEqual(
      $this->cache_manager->getCacheId(),
      'f_' . md5('/root/test' . serialize(array()))
    );
  }

  function testGetCacheIdJunkyAttributes()
  {
    $rule = array('optional' => array('pager'), 'required' => array('action'));

    $this->request->setReturnValue('export', $query_items = array('action' => 1, 'pager' => 1, 'extra' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->cache_manager->setReturnValue('_getMatchedRule', $rule);

    $this->assertEqual(
      $this->cache_manager->getCacheId(),
      'f_' . md5('/root/test' . serialize(array('action' => 1, 'pager' => 1)))
    );
  }

  function testCacheDoesntExistNoUri()
  {
    $cache_manager = new FullPageCacheManager();
    $this->assertFalse($cache_manager->cacheExists());
  }

  function testCacheExists()
  {
    $this->_writeCache($path = '/root/test', $attributes = array('action' => 1));

    $this->uri->setReturnValue('getPath', $path);
    $this->request->setReturnValue('export', $attributes);
    $this->cache_manager->setReturnValue('_getMatchedRule', array('optional' => array('action')));

    $this->assertTrue($this->cache_manager->cacheExists());

    $this->_cleanCache($path, $attributes);
  }

  function testCacheDoesntExistParamsDontMatch()
  {
    $this->_writeCache($path = '/root/test', $attributes = array('action' => 2));

    $this->uri->setReturnValue('getPath', '/root/test');
    $this->request->setReturnValue('export', array('action' => 1));

    $this->cache_manager->setReturnValue('_getMatchedRule', array('optional' => array('action')));

    $this->assertFalse($this->cache_manager->cacheExists());

    $this->_cleanCache($path, $attributes);
  }

  function testCacheDoesntExistParamsDontMatch2()
  {
    $this->_writeCache($path = '/root/test', $attributes = array('action' => 1, 'page' => 1));

    $this->uri->setReturnValue('getPath', '/root/test');
    $this->request->setReturnValue('export', array('action' => 1));

    $this->cache_manager->setReturnValue('_getMatchedRule', array('optional' => array('action')));

    $this->assertFalse($this->cache_manager->cacheExists());

    $this->_cleanCache($path, $attributes);
  }

  function testGetFalseNoUri()
  {
    $cache_manager = new FullPageCacheManager();
    $this->assertFalse($cache_manager->get());
  }

  function testGet()
  {
    $cache_manager = new FullPageCacheManagerTestVersion2($this);
    $cache_manager->setRequest($this->request);

    $this->_writeSimpleCache($cache_id = 1, $contents = 'test-test');

    $cache_manager->setReturnValue('isCacheable', true);
    $cache_manager->setReturnValue('cacheExists', true);
    $cache_manager->setReturnValue('getCacheId', $cache_id);

    $this->assertEqual($cache_manager->get(), $contents);

    $cache_manager->tally();

    $this->_cleanSimpleCache($cache_id);
  }

  function testWriteFalseNoUri()
  {
    $cache_manager = new FullPageCacheManager();
    $this->assertFalse($cache_manager->write($content = 'test'));
  }

  function testWrite()
  {
    $cache_manager = new FullPageCacheManagerTestVersion2($this);
    $cache_manager->setRequest($this->request);

    $contents = 'test-test';

    $cache_manager->setReturnValue('getCacheId', $cache_id = 1);

    $this->assertTrue($cache_manager->write($contents));
    $this->assertEqual($this->_readSimpleCache($cache_id), $contents);

    $cache_manager->tally();

    $this->_cleanSimpleCache($cache_id);
  }

  function testOverwrite()
  {
    $this->_writeSimpleCache($cache_id = 1, $contents = 'test-overwrite');

    $cache_manager = new FullPageCacheManagerTestVersion2($this);
    $cache_manager->setRequest($this->request);

    $contents = 'test-test';

    $cache_manager->setReturnValue('getCacheId', $cache_id);

    $this->assertTrue($cache_manager->write($contents));
    $this->assertEqual($this->_readSimpleCache($cache_id), $contents);

    $this->_cleanSimpleCache($cache_id);

    $cache_manager->tally();
  }

  function testFlush()
  {
    $this->_writeSimpleCache('f_test1', $content1 = 'test-content1');
    $this->_writeSimpleCache('f_test2', $content2 ='test-content2');
    $this->_writeSimpleCache('not_page_file', $content3 ='test-content3');

    $cache_manager = new FullPageCacheManager();
    $cache_manager->flush();

    $files = Fs :: findSubitems(PAGE_CACHE_DIR);

    $this->assertEqual(sizeof($files), 1);

    $file = reset($files);
    $this->assertEqual(Fs :: cleanPath($file), Fs :: cleanPath(PAGE_CACHE_DIR . Fs :: separator() . 'not_page_file'));

    $this->_cleanSimpleCache('not_page_file');
  }

  function _writeCache($path, $attributes, $contents='test')
  {
    $file_id = 'f_' . md5($path . serialize($attributes));
    $this->_writeSimpleCache($file_id, $contents);
  }

  function _writeSimpleCache($file_id, $contents)
  {
    Fs :: mkdir(PAGE_CACHE_DIR);
    $f = fopen(PAGE_CACHE_DIR . $file_id, 'w');
    fwrite($f, $contents);
    fclose($f);
  }

  function _readSimpleCache($file_id)
  {
    return file_get_contents(PAGE_CACHE_DIR . $file_id);
  }

  function _cleanSimpleCache($file_id)
  {
    if(file_exists(PAGE_CACHE_DIR . $file_id))
      unlink(PAGE_CACHE_DIR . $file_id);
  }

  function _cleanCache($path, $attributes)
  {
    $file_id = 'f_' . md5($path . serialize($attributes));
    $this->_cleanSimpleCache($file_id);
  }
}

?>