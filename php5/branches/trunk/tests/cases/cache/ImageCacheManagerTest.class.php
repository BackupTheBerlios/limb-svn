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
require_once(LIMB_DIR . '/class/cache/ImageCacheManager.class.php');
require_once(LIMB_DIR . '/class/core/request/Request.class.php');
require_once(LIMB_DIR . '/class/lib/http/Uri.class.php');
require_once(LIMB_DIR . '/class/core/permissions/User.class.php');
require_once(LIMB_DIR . '/class/core/datasources/SiteObjectsByNodeIdsDatasource.class.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');

Mock :: generate('Uri');
Mock :: generate('SiteObjectsByNodeIdsDatasource');
Mock :: generate('LimbToolkit');

Mock :: generatePartial(
  'ImageCacheManager',
  'ImageCacheManagerTestVersion',
  array('getRules', '_setMatchedRule', '_getMatchedRule', '_isUserInGroups')
);

Mock :: generatePartial(
  'ImageCacheManager',
  'ImageCacheManagerTestVersion2',
  array('isCacheable', '_cacheMediaFile', '_isImageCached', '_getCachedImageExtension')
);

Mock :: generatePartial(
  'ImageCacheManager',
  'ImageCacheManagerTestVersion3',
  array('isCacheable')
);

class ImageCacheManagerTest extends LimbTestCase
{
  var $cache_manager;
  var $cache_manager2;
  var $uri;
  var $datasource;
  var $toolkit;

  function setUp()
  {
    $this->uri = new MockUri($this);
    $this->datasource = new MockSiteObjectsByNodeIdsDatasource($this);

    $this->cache_manager = new ImageCacheManagerTestVersion($this);
    $this->cache_manager->setUri($this->uri);

    $this->cache_manager2 = new ImageCacheManagerTestVersion2($this);
    $this->cache_manager2->setReturnValue('isCacheable', true);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getDatasource',
                                   $this->datasource,
                                   array('SiteObjectsByNodeIdsDatasource'));

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->cache_manager->tally();
    $this->cache_manager2->tally();
    $this->uri->tally();
    $this->datasource->tally();

    Limb :: popToolkit();
  }

  function testGetRulesFromIni()
  {
    $cache_manager = new ImageCacheManager();

    registerTestingIni(
      'image_cache.ini',
      '
      [rule1]
       path_regex = /root/test1
      [rule2]
       path_regex = /root/test2
       groups[] = members
       groups[] = visitors
      [not_valid_rule]
       path_regex = /root/test3
      '
    );

    $this->toolkit->setReturnValue('getINI',
                                   getIni('image_cache.ini'),
                                   array('image_cache.ini'));

    $this->assertEqual($cache_manager->getRules(),
      array(
        array('path_regex' => '/root/test1'),
        array('path_regex' => '/root/test2', 'groups' => array('members', 'visitors'))
      )
    );

    clearTestingIni();
  }

  function testIsNotCacheableNoUri()
  {
    $cache_manager = new ImageCacheManager();

    $this->assertFalse($cache_manager->isCacheable());
  }

  function testIsCacheableGroupsMatch()
  {
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->cache_manager->setReturnValueAt(0, '_isUserInGroups', true, array(array('members', 'visitors')));

    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
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
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->cache_manager->setReturnValueAt(0, '_isUserInGroups', false, array(array('members')));

    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
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
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->uri->setReturnValue('getPath', '/root/test');

    $this->assertTrue($this->cache_manager->isCacheable());
    $this->cache_manager->expectOnce('_setMatchedRule', array($rule));
  }

  function testIsNotCacheableDeny()
  {
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'type' => 'deny'
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

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

    $this->uri->setReturnValue('getQueryItems', array('action' => 1, 'pager' => 1, 'extra' => 1));
    $this->uri->setReturnValue('getPath', '/root/test');

    $this->assertTrue($this->cache_manager->isCacheable());
    $this->cache_manager->expectOnce('_setMatchedRule', array($rule));
  }

  function testIsNotCacheablePathDoesntMatch()
  {
    $rule = array(
               'path_regex' => '/^\/root\/test.*$/',
               'type' => 'allow'
              );

    $this->cache_manager->setReturnValue('getRules',
      array($rule)
    );

    $this->uri->setReturnValue('getPath', '/root/tesx');

    $this->assertFalse($this->cache_manager->isCacheable());
    $this->cache_manager->expectNever('_setMatchedRule');
  }

  function testIsCacheableSecondRuleMatch()
  {
    $rule1 = array(
           'path_regex' => '/^\/root\/test1.*$/',
           'type' => 'allow'
    );

    $rule2 = array(
           'path_regex' => '/^\/root\/test2.*$/',
           'type' => 'allow'
    );

    $rule3 = array(
           'path_regex' => '/^\/root\/tes3.*$/',
           'type' => 'allow'
    );

    $this->cache_manager->setReturnValue('getRules',
      array($rule1, $rule2, $rule3)
    );

    $this->uri->setReturnValue('getPath', '/root/test2');

    $this->assertTrue($this->cache_manager->isCacheable());
    $this->cache_manager->expectOnce('_setMatchedRule', array($rule2));
  }

  function testIsNotCacheableSecondRuleDeny()
  {
    $rule1 = array(
           'path_regex' => '/^\/root\/test1.*$/',
           'type' => 'allow'
    );

    $rule2 = array(
           'path_regex' => '/^\/root\/test2.*$/',
           'type' => 'deny'
    );

    $this->cache_manager->setReturnValue('getRules',
                                         array($rule1, $rule2));

    $this->uri->setReturnValue('getPath', '/root/test2');

    $this->assertFalse($this->cache_manager->isCacheable());
    $this->cache_manager->expectNever('_setMatchedRule');
  }

  function testProcessEmptyContent()
  {
    $this->cache_manager2->processContent($c = '');
    $this->assertIdentical($c, '');
    $this->toolkit->expectNever('getDatasource');
  }

  function testProcessImgTag()
  {
    $c = '<p><img alt="test" src="/root?node_id=1" border="0"></p>';

    $this->cache_manager2->expectOnce('_isImageCached');
    $this->cache_manager2->setReturnvalue('_is_image_cached', false);

    $this->datasource->expectOnce('setNodeIds', array(array(1)));
    $this->datasource->expectOnce('setSiteObjectClassName', array('ImageObject'));
    $this->datasource->expectOnce('fetch');

    $this->datasource->setReturnValue('fetch',
      array(
        1 => array(
          'identifier' => 'testImage',
          'variations' => array(
            'thumbnail' => array(
              'mediaId' => 200,
              'mimeType' => 'image/jpeg'
            ),
          )
        )
      )
    );

    $this->cache_manager2->expectOnce('_cacheMediaFile', array(200, '1thumbnail.jpg'));
    $this->cache_manager2->processContent($c);

    $this->assertEqual($c, "<p><img alt=\"test\" src='" . IMAGE_CACHE_WEB_DIR . "1thumbnail.jpg' border=\"0\"></p>");
  }


  function testProcessImgTagSomeImagesCached()
  {
    $c = '<p><img alt="test" src="/root?node_id=1" border="0"></p><img alt="test" src="/root?node_id=2" border="0">';

    $this->cache_manager2->expectCallCount('_isImageCached', 2);
    $this->cache_manager2->expectCallCount('_getCachedImageExtension', 2);

    $this->cache_manager2->setReturnValue('_isImageCached', true, array(2, 'thumbnail'));
    $this->cache_manager2->setReturnValue('_isImageCached', false, array(2, 'thumbnail'));

    $this->cache_manager2->setReturnValue('_getCachedImageExtension', '.jpg', array(2, 'thumbnail'));
    $this->cache_manager2->setReturnValue('_getCachedImageExtension', false, array(2, 'thumbnail'));

    $this->datasource->expectOnce('setNodeIds', array(array(1)));
    $this->datasource->expectOnce('setSiteObjectClassName', array('ImageObject'));
    $this->datasource->expectOnce('fetch');

    $this->datasource->setReturnValue('fetch',
      array(
        1 => array(
          'identifier' => 'testImage',
          'variations' => array(
            'thumbnail' => array(
              'mediaId' => 200,
              'mimeType' => 'image/jpeg'
            ),
          )
        )
      )
    );

    $this->cache_manager2->expectOnce('_cacheMediaFile', array(200, '1thumbnail.jpg'));
    $this->cache_manager2->processContent($c);

    $this->assertEqual($c,
      '<p><img alt="test" src=\'' . IMAGE_CACHE_WEB_DIR .
      '1thumbnail.jpg\' border="0"></p><img alt="test" src=\'' . IMAGE_CACHE_WEB_DIR .
      '2thumbnail.jpg\' border="0">');
  }

  function testProcessContentBackgroundAttribute()
  {
    $c = '<td width="1" background="/root?node_id=1" border="0"></td>';

    $this->cache_manager2->expectOnce('_isImageCached');
    $this->cache_manager2->setReturnvalue('_is_image_cached', false);

    $this->datasource->expectOnce('setNodeIds', array(array(1)));
    $this->datasource->expectOnce('setSiteObjectClassName', array('ImageObject'));
    $this->datasource->expectOnce('fetch');

    $this->datasource->setReturnValue('fetch',
      array(
        1 => array(
          'identifier' => 'testImage',
          'variations' => array(
            'thumbnail' => array(
              'mediaId' => 200,
              'mimeType' => 'image/jpeg'
            ),
          )
        )
      )
    );

    $this->cache_manager2->expectOnce('_cacheMediaFile', array(200, '1thumbnail.jpg'));
    $this->cache_manager2->processContent($c);

    $this->assertEqual($c, "<td width=\"1\" background='" . IMAGE_CACHE_WEB_DIR . "1thumbnail.jpg' border=\"0\"></td>");
  }

  function testProcessContentQuotesProperHandling()
  {
    $c = '<p><img alt="test" src=\'/root?node_id=1" border="0"></p>';

    $this->cache_manager2->expectOnce('_isImageCached');
    $this->cache_manager2->setReturnvalue('_is_image_cached', false);

    $this->datasource->expectOnce('setNodeIds', array(array(1)));
    $this->datasource->expectOnce('setSiteObjectClassName', array('ImageObject'));
    $this->datasource->expectOnce('fetch');

    $this->datasource->setReturnValue('fetch',
      array(
        1 => array(
          'identifier' => 'testImage',
          'variations' => array(
            'thumbnail' => array(
              'mediaId' => 200,
              'mimeType' => 'image/jpeg'
            ),
          )
        )
      )
    );

    $this->cache_manager2->expectOnce('_cacheMediaFile', array(200, '1thumbnail.jpg'));
    $this->cache_manager2->processContent($c);

    $this->assertEqual($c, "<p><img alt=\"test\" src='" . IMAGE_CACHE_WEB_DIR . "1thumbnail.jpg' border=\"0\"></p>");
  }

  function testProcessContentOriginalVariation()
  {
    $c = '<p><img alt="test" src="/root?node_id=1&original" border="0"></p>';

    $this->cache_manager2->expectOnce('_isImageCached');
    $this->cache_manager2->setReturnvalue('_is_image_cached', false);

    $this->datasource->expectOnce('setNodeIds', array(array(1)));
    $this->datasource->expectOnce('setSiteObjectClassName', array('ImageObject'));
    $this->datasource->expectOnce('fetch');

    $this->datasource->setReturnValue('fetch',
      array(
        1 => array(
          'identifier' => 'testImage',
          'variations' => array(
            'original' => array(
              'mediaId' => 100,
              'mimeType' => 'image/jpeg'
            ),
          )
        )
      )
    );

    $this->cache_manager2->expectOnce('_cacheMediaFile', array(100, '1original.jpg'));
    $this->cache_manager2->processContent($c);

    $this->assertEqual($c, "<p><img alt=\"test\" src='" . IMAGE_CACHE_WEB_DIR . "1original.jpg' border=\"0\"></p>");
  }

  function testProcessContentImgAndBackgroundSameImageObject()
  {
    $c = '<img src="/root?node_id=1"><br><td width="1" background="/root?node_id=1&icon" border="0"></td>';

    $this->cache_manager2->expectCallCount('_isImageCached', 2);
    $this->cache_manager2->setReturnvalue('_is_image_cached', false);

    $this->datasource->expectOnce('setNodeIds', array(array(1)));
    $this->datasource->expectOnce('setSiteObjectClassName', array('ImageObject'));
    $this->datasource->expectOnce('fetch');

    $this->datasource->setReturnValue('fetch',
      array(
        1 => array(
          'identifier' => 'testImage',
          'variations' => array(
            'thumbnail' => array(
              'mediaId' => 200,
              'mimeType' => 'image/jpeg'
            ),
            'icon' => array(
              'mediaId' => 300,
              'mimeType' => 'image/jpeg'
            ),
          )
        )
      )
    );

    $this->cache_manager2->expectArgumentsAt(0, '_cacheMediaFile', array(200, '1thumbnail.jpg'));
    $this->cache_manager2->expectArgumentsAt(1, '_cacheMediaFile', array(300, '1icon.jpg'));

    $this->cache_manager2->processContent($c);

    $this->assertEqual($c, "<img src='" .
      IMAGE_CACHE_WEB_DIR . "1thumbnail.jpg'><br><td width=\"1\" background='" .
      IMAGE_CACHE_WEB_DIR . "1icon.jpg' border=\"0\"></td>");
  }

  function testWriteCachedFile()
  {
    $c = '<p><img alt="test" src="/root?node_id=1&icon" border="0"></p>';

    $cache_manager = new ImageCacheManagerTestVersion3($this);
    $cache_manager->setReturnValue('isCacheable', true);

    $this->datasource->expectOnce('setNodeIds', array(array(1)));
    $this->datasource->expectOnce('setSiteObjectClassName', array('ImageObject'));
    $this->datasource->expectOnce('fetch');

    $this->datasource->setReturnValue('fetch',
      array(
        1 => array(
          'identifier' => 'testImage',
          'variations' => array(
            'icon' => array(
              'mediaId' => 200,
              'mimeType' => 'image/jpeg'
            ),
          )
        )
      )
    );

    $this->_writeMediaFile(200);
    $cache_manager->processContent($c);

    $this->assertTrue(file_exists(IMAGE_CACHE_DIR . '1icon.jpg'));

    $this->_cleanUp();
  }

  function testCachedWriteFileNoMediaFile()
  {
    $cache_manager = new ImageCacheManagerTestVersion3($this);
    $cache_manager->setReturnValue('isCacheable', true);

    $this->datasource->expectOnce('setNodeIds', array(array(1)));
    $this->datasource->expectOnce('setSiteObjectClassName', array('ImageObject'));
    $this->datasource->expectOnce('fetch');

    $this->datasource->setReturnValue('fetch',
      array(
        1 => array(
          'identifier' => 'testImage',
          'variations' => array(
            'icon' => array(
              'mediaId' => 200,
              'mimeType' => 'image/jpeg'
            ),
          )
        )
      )
    );

    $c = '<p><img alt="test" src="/root?node_id=1&icon" border="0"></p>';

    $cache_manager->processContent($c);

    $this->assertEqual(sizeof(Fs :: ls(IMAGE_CACHE_DIR)), 0);

    $this->_cleanUp();
  }

  function _writeMediaFile($media_id)
  {
    Fs :: mkdir(MEDIA_DIR);

    $f = fopen(MEDIA_DIR . $media_id . '.media', 'w');
    fwrite($f, 'test');
    fclose($f);
  }

  function _cleanUp()
  {
    Fs :: rm(IMAGE_CACHE_DIR);
    Fs :: rm(MEDIA_DIR);
  }
}

?>