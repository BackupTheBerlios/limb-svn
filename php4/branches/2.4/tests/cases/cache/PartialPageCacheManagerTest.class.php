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
require_once(LIMB_DIR . '/class/cache/PartialPageCacheManager.class.php');
require_once(LIMB_DIR . '/class/core/request/Request.class.php');
require_once(LIMB_DIR . '/class/lib/http/Uri.class.php');

Mock :: generate('Uri');
Mock :: generate('Request');

Mock :: generatePartial(
  'PartialPageCacheManager',
  'PartialPageCacheManagerTestVersion',
  array('getRules', '_setMatchedRule', '_getMatchedRule', '_isUserInGroups')
);

Mock :: generatePartial(
  'PartialPageCacheManager',
  'PartialPageCacheManagerTestVersion2',
  array('isCacheable', 'cacheExists', 'getCacheId')
);


class PartialPageCacheManagerTest extends LimbTestCase
{
  var $cache_manager;
  var $uri;
  var $request;

  function setUp()
  {
    $this->uri = new MockUri($this);
    $this->request = new MockRequest($this);

    $this->request->setReturnValue('getUri', $this->uri);

    $this->cache_manager = new PartialPageCacheManagerTestVersion($this);
    $this->cache_manager->setRequest($this->request);
  }

  function tearDown()
  {
    $this->cache_manager->tally();
    $this->uri->tally();
    $this->request->tally();
  }

  function testGetRulesFromIni()
  {
    $cache_manager = new PartialPageCacheManager();

    registerTestingIni(
      'partial_page_cache.ini',
      '
      [rule1]
       server_id = test1
       optional[] = test1
       required[] = test2
      [rule2]
       server_id = wow
       groups[] = members
       groups[] = visitors
      [not_valid_rule]
       bla-bla = bla-bla
      '
    );

    $this->assertEqual($cache_manager->getRules(),
      array(
        array('server_id' => 'test1', 'optional' => array('test1'), 'required' => array('test2')),
        array('server_id' => 'wow', 'groups' => array('members', 'visitors'))
      )
    );

    clearTestingIni();
  }

  function testIsNotCacheableNoUri()
  {
    $cache_manager = new PartialPageCacheManager();

    $this->assertFalse($cache_manager->isCacheable());
  }

  function testIsNotCacheableIfNoRules()
  {
    $this->request->setReturnValue('export', array('pager' => 1));

    $this->cache_manager->setReturnValue('getRules',  array());
    $this->assertFalse($this->cache_manager->isCacheable());
  }

  function testIsNotCacheableNoRuleForServerId()
  {
    $this->request->setReturnValue('export', array('action' => 1, 'pager' => 1));

    $rule1 = array(
               'server_id' => 'last_news',
               'optional' => array('action', 'pager'),
               'type' => 'allow'
              );
    $rule2 = array(
               'server_id' => 'last_docs',
               'optional' => array('action', 'pager'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule1, $rule2)
    );

    $this->cache_manager->setServerId('no_such_server_id');

    $this->assertFalse($this->cache_manager->isCacheable());
    $this->cache_manager->expectNever('_setMatchedRule');
  }

  function testIsCacheableAllowByDefault()
  {
    $rule = array(
               'server_id' => 'last_news',
               'optional' => array('action', 'pager'),
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->request->setReturnValue('export', array('action' => 1, 'pager' => 1));

    $this->cache_manager->setServerId('last_news');

    $this->assertTrue($this->cache_manager->isCacheable());
    $this->cache_manager->expectOnce('_setMatchedRule', array($rule));
  }

  function testIsCacheableOptionalAttributes()
  {
    $rule = array(
               'server_id' => 'last_news',
               'optional' => array('action', 'pager'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->request->setReturnValue('export', array('action' => 1, 'pager' => 1));

    $this->cache_manager->setServerId('last_news');

    $this->assertTrue($this->cache_manager->isCacheable());
    $this->cache_manager->expectOnce('_setMatchedRule', array($rule));
  }

  function testIsCacheableRequiredAttributes()
  {
    $rule = array(
               'server_id' => 'last_news',
               'required' => array('action', 'pager'),
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->request->setReturnValue('export', array('action' => 1, 'pager' => 1));

    $this->cache_manager->setServerId('last_news');

    $this->assertTrue($this->cache_manager->isCacheable());
    $this->cache_manager->expectOnce('_setMatchedRule', array($rule));
  }

  function testIsCacheableGroupsMatch()
  {
    $this->request->setReturnValue('export', array('action' => 1, 'pager' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->cache_manager->setReturnValueAt(0, '_isUserInGroups', true, array(array('members', 'visitors')));

    $rule = array(
               'server_id' => 'last_news',
               'optional' => array('action', 'pager'),
               'type' => 'allow',
               'groups' => array('members', 'visitors')
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->cache_manager->setServerId('last_news');

    $this->assertTrue($this->cache_manager->isCacheable());
    $this->cache_manager->expectOnce('_setMatchedRule', array($rule));
  }

  function testIsNotCacheableGroupsDontMatch()
  {
    $this->request->setReturnValue('export', array('action' => 1, 'pager' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->cache_manager->setReturnValueAt(0, '_isUserInGroups', false, array(array('members', 'visitors')));

    $rule = array(
               'server_id' => 'last_news',
               'optional' => array('action', 'pager'),
               'type' => 'allow',
               'groups' => array('members', 'visitors')
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->cache_manager->setServerId('last_news');

    $this->assertFalse($this->cache_manager->isCacheable());
    $this->cache_manager->expectNever('_setMatchedRule');
  }

  function testIsCacheableExtraAttributes()
  {
    $rule = array(
               'server_id' => 'last_news',
               'optional' => array('action', 'pager'),
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->request->setReturnValue('export', array('action' => 1, 'pager' => 1, 'extra' => 1));

    $this->cache_manager->setServerId('last_news');

    $this->assertTrue($this->cache_manager->isCacheable());
    $this->cache_manager->expectOnce('_setMatchedRule', array($rule));
  }

  function testIsNotCacheableDeny()
  {
    $rule = array(
               'server_id' => 'last_news',
               'optional' => array('action', 'pager'),
               'type' => 'deny'
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->request->setReturnValue('export', array('action' => 1, 'pager' => 1));

    $this->cache_manager->setServerId('last_news');

    $this->assertFalse($this->cache_manager->isCacheable());
    $this->cache_manager->expectNever('_setMatchedRule');
  }

  function testIsNotCacheableAttributesDontMatch()
  {
    $rule = array(
               'server_id' => 'last_news',
               'required' => array('action', 'pager'),
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->request->setReturnValue('export', array('action' => 1));

    $this->cache_manager->setServerId('last_news');

    $this->assertFalse($this->cache_manager->isCacheable());
    $this->cache_manager->expectNever('_setMatchedRule');
  }

  function testGetCacheId()
  {
    $rule = array('optional' => array('action'), 'server_id' => 'last_news');

    $this->request->setReturnValue('export', $query_items = array('action' => 1));

    $this->cache_manager->setReturnValue('_getMatchedRule', $rule);

    $this->assertEqual(
      $this->cache_manager->getCacheId(),
      'p_' . md5( 'last_news' . serialize($query_items))
    );
  }

  function testGetCacheIdQueryItemsSorted()
  {
    $rule = array('server_id' => 'test', 'optional' => array('pager', 'prop'), 'required' => array('action'));

    $this->request->setReturnValue('export', $query_items = array('action' => 1, 'pager' => 2));

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

  function testGetCacheIdUsePath()
  {
    $rule = array('optional' => array('action'), 'server_id' => 'last_news', 'use_path' => true);

    $this->request->setReturnValue('export', $query_items = array('action' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->cache_manager->setReturnValue('_getMatchedRule', $rule);

    $this->assertEqual(
      $this->cache_manager->getCacheId(),
      'p_' . md5('last_news' . '/root/test' . serialize($query_items))
    );
  }

  function testGetCacheIdExtraAttributes()
  {
    $rule = array('optional' => array('action'), 'server_id' => 'last_news');

    $this->request->setReturnValue('export', $query_items = array('action' => 1, 'extra' => 1));

    $this->cache_manager->setReturnValue('_getMatchedRule', $rule);

    $this->assertEqual(
      $this->cache_manager->getCacheId(),
      'p_' . md5( 'last_news' . serialize(array('action' => 1)))
    );
  }

  function testGetCacheIdNoAttributes()
  {
    $rule = array('server_id' => 'last_news');

    $this->request->setReturnValue('export', $query_items = array('action' => 1));

    $this->cache_manager->setReturnValue('_getMatchedRule', $rule);

    $this->assertEqual(
      $this->cache_manager->getCacheId(),
      'p_' . md5('last_news' . serialize(array()))
    );
  }

  function testCacheDoesntExistNoUri()
  {
    $cache_manager = new PartialPageCacheManager();
    $this->assertFalse($cache_manager->cacheExists());
  }

  function testCacheExists()
  {
    $this->_writeCache($server_id = 'last_news', $attributes = array('action' => 1));

    $this->request->setReturnValue('export', $attributes);
    $this->cache_manager->setReturnValue('_getMatchedRule', array('serverId' => $server_id, 'optional' => array('action')));

    $this->assertTrue($this->cache_manager->cacheExists());

    $this->_cleanCache($server_id, $attributes);
  }

  function testCacheExistsUsePath()
  {
    $this->_writeCache($server_id = 'last_news', $attributes = array('action' => 1), 'test', $path = '/root/test');

    $this->request->setReturnValue('export', $attributes);
    $this->uri->setReturnValue('getPath', '/root/test');
    $this->cache_manager->setReturnValue('_getMatchedRule', array('serverId' => $server_id, 'usePath' => true, 'optional' => array('action')));

    $this->assertTrue($this->cache_manager->cacheExists());

    $this->_cleanCache($server_id, $attributes, $path);
  }

  function testGet()
  {
    $cache_manager = new PartialPageCacheManagerTestVersion2($this);
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
    $cache_manager = new PartialPageCacheManager();
    $this->assertFalse($cache_manager->write($content = 'test'));
  }

  function testWrite()
  {
    $cache_manager = new PartialPageCacheManagerTestVersion2($this);
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

    $cache_manager = new PartialPageCacheManagerTestVersion2($this);
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
    $this->_writeSimpleCache('p_test1', $content1 = 'test-content1');
    $this->_writeSimpleCache('p_test2', $content2 ='test-content2');
    $this->_writeSimpleCache('not_page_file', $content3 ='test-content3');

    $cache_manager = new PartialPageCacheManager();
    $cache_manager->flush();

    $files = Fs :: findSubitems(PAGE_CACHE_DIR);

    $this->assertEqual(sizeof($files), 1);

    $file = reset($files);
    $this->assertEqual(Fs :: cleanPath($file), Fs :: cleanPath(PAGE_CACHE_DIR . Fs :: separator() . 'not_page_file'));

    $this->_cleanSimpleCache('not_page_file');
  }

  function _writeCache($server_id, $attributes, $contents='test', $path = '')
  {
    if ($path)
      $file_id = 'p_' . md5($server_id . $path . serialize($attributes));
    else
      $file_id = 'p_' . md5($server_id . serialize($attributes));

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

  function _cleanCache($server_id, $attributes, $path = '')
  {
    if ($path)
      $file_id = 'p_' . md5($server_id . $path . serialize($attributes));
    else
      $file_id = 'p_' . md5($server_id . serialize($attributes));

    $this->_cleanSimpleCache($file_id);
  }
}

?>