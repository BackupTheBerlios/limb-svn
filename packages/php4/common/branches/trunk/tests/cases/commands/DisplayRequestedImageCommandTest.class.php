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
require_once(dirname(__FILE__) . '/../../../commands/images/DisplayRequestedImageCommand.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/request/HttpResponse.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/request/HttpCache.class.php');
require_once(LIMB_DIR . '/core/datasources/RequestedObjectDatasource.class.php');
require_once(LIMB_DIR . '/core/util/Ini.class.php');

Mock :: generate('LimbBaseToolkit', 'MockLimbToolkit');
Mock :: generate('HttpResponse');
Mock :: generate('Request');
Mock :: generate('HttpCache');
Mock :: generate('Ini');
Mock :: generate('RequestedObjectDatasource');

Mock :: generatePartial('DisplayRequestedImageCommand',
                        'DisplayImageCommandTestVersion',
                        array('_getHttpCache'));

class DisplayRequestedImageCommandTest extends LimbTestCase
{
  var $command;
  var $toolkit;
  var $response;
  var $request;
  var $ini;
  var $datasource;
  var $cache;

  function setUp()
  {
    $this->command = new DisplayImageCommandTestVersion($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->response = new MockHttpResponse($this);
    $this->cache = new MockHttpCache($this);
    $this->request = new MockRequest($this);
    $this->ini = new MockIni($this);
    $this->datasource = new MockRequestedObjectDatasource($this);

    $this->command->setReturnReference('_getHttpCache', $this->cache);

    $this->toolkit->setReturnReference('getResponse', $this->response);
    $this->toolkit->setReturnReference('getRequest', $this->request);
    $this->toolkit->setReturnReference('getDatasource', $this->datasource, array('RequestedObjectDatasource'));
    $this->toolkit->setReturnValue('getINI', $this->ini, array('image_variations.ini'));

    $this->datasource->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->datasource->expectOnce('fetch');

    $this->ini->setReturnValue('getAll', array('original' => array(),
                                                'thumbnail' => array(),
                                                'icon' => array()));

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->response->tally();
    $this->request->tally();
    $this->datasource->tally();
    $this->ini->tally();
    $this->cache->tally();

    Limb :: restoreToolkit();
  }

  function testPerformObjectNotFetched()
  {
    $this->datasource->setReturnValue('fetch', array());
    $this->ini->expectNever('getAll');

    $this->response->expectNever('commit');

    $this->assertEqual($this->command->perform(), LIMB_STATUS_ERROR);
  }

  function testPerformNoSuchVariationOriginal()
  {
    $this->datasource->setReturnValue('fetch', $object_data = array('id' => 100));

    $this->ini->expectOnce('getAll');

    $this->request->expectOnce('hasAttribute', array('original'));
    $this->request->setReturnValue('hasAttribute', true, array('original'));

    $this->response->expectNever('commit');

    $this->assertEqual($this->command->perform(), LIMB_STATUS_ERROR);
  }

  function testPerformNoSuchVariationNotOriginal()
  {
    $this->datasource->setReturnValue('fetch', $object_data = array('id' => 100));

    $this->ini->expectOnce('getAll');

    $this->request->setReturnValue('hasAttribute', true, array('icon'));

    $this->response->expectOnce('header', array('content-type: image/gif'));
    $this->response->expectOnce('readfile', array(HTTP_SHARED_DIR . 'images/1x1.gif'));
    $this->response->expectOnce('commit');

    $this->command->perform();
  }

  function testPerformNoMediaFileOriginal()
  {
    $object_data = array('id' => 100,
                         'variations' => array('original' => array('media_id' => 'fxfxfxfx')));

    $this->datasource->setReturnValue('fetch', $object_data);

    $this->ini->expectOnce('getAll');

    $this->request->setReturnValue('hasAttribute', true, array('original'));

    $this->response->expectNever('commit');

    $this->assertEqual($this->command->perform(), LIMB_STATUS_ERROR);
  }

  function testPerformNoMediaFileNotOriginal()
  {
    $object_data = array('id' => 100,
                         'variations' => array('icon' => array('media_id' => 'fxfxfxfx')));

    $this->datasource->setReturnValue('fetch', $object_data);

    $this->ini->expectOnce('getAll');

    $this->request->setReturnValue('hasAttribute', true, array('icon'));

    $this->response->expectOnce('header', array('hTTP/1.1 404 Not found'));
    $this->response->expectOnce('commit');

    $this->command->perform();
  }

  function testPerformHttpCacheHitOriginal()
  {
    $object_data = array('id' => 100,
                         'modified_date' => $time = time(),
                         'variations' => array('original' => array('media_id' => $media_id = 'fxfxfxfx',
                                                                   'mime_type' => $mime_type = 'jpeg')));

    $this->datasource->setReturnValue('fetch', $object_data);

    $this->request->setReturnValue('hasAttribute', true, array('original'));

    $this->cache->expectOnce('setLastModifiedTime', array($time));
    $this->cache->expectOnce('setCacheTime', array(DisplayRequestedImageCommand :: DAY_CACHE));
    $this->cache->expectOnce('checkAndWrite', array(new IsAExpectation('MockHttpResponse')));
    $this->cache->setReturnValue('checkAndWrite', true);

    $this->response->expectNever('readfile');
    $this->response->expectOnce('header', array("Content-type: {$mime_type}"));
    $this->response->expectNever('commit');

    $this->_createTmpMedia($media_id);

    $this->assertEqual($this->command->perform(), LIMB_STATUS_OK);

    $this->_removeTmpMedia($media_id);
  }

  function testPerformHttpCacheHitNotOriginal()
  {
    $object_data = array('id' => 100,
                         'modified_date' => $time = time(),
                         'variations' => array('icon' => array('media_id' => $media_id = 'fxfxfxfx',
                                                               'mime_type' => $mime_type = 'jpeg')));

    $this->datasource->setReturnValue('fetch', $object_data);

    $this->request->setReturnValue('hasAttribute', true, array('icon'));

    $this->cache->expectOnce('setLastModifiedTime', array($time));
    $this->cache->expectOnce('setCacheTime', array(DisplayRequestedImageCommand :: DAY_CACHE));
    $this->cache->expectOnce('checkAndWrite', array(new IsAExpectation('MockHttpResponse')));
    $this->cache->setReturnValue('checkAndWrite', true);

    $this->response->expectNever('readfile');
    $this->response->expectOnce('header', array("Content-type: {$mime_type}"));
    $this->response->expectOnce('commit');

    $this->_createTmpMedia($media_id);

    $this->command->perform();

    $this->_removeTmpMedia($media_id);
  }

  function testPerformHttpCacheMiss()
  {
    $object_data = array('id' => 100,
                         'modified_date' => $time = time(),
                         'variations' => array('icon' => array('media_id' => $media_id = 'fxfxfxfx',
                                                               'mime_type' => $mime_type = 'jpeg',
                                                               'file_name' => $file_name = 'test file')));

    $this->datasource->setReturnValue('fetch', $object_data);

    $this->request->setReturnValue('hasAttribute', true, array('icon'));

    $this->cache->expectOnce('setLastModifiedTime', array($time));
    $this->cache->expectOnce('setCacheTime', array(DisplayRequestedImageCommand :: DAY_CACHE));
    $this->cache->expectOnce('checkAndWrite', array(new IsAExpectation('MockHttpResponse')));
    $this->cache->setReturnValue('checkAndWrite', false);

    $this->response->expectOnce('readfile', array(MEDIA_DIR. $media_id .'.media'));
    $this->response->expectArgumentsAt(0, 'header', array("Content-Disposition: filename={$file_name}"));
    $this->response->expectArgumentsAt(1, 'header', array("Content-type: {$mime_type}"));
    $this->response->expectOnce('commit');

    $this->_createTmpMedia($media_id);

    $this->command->perform();

    $this->_removeTmpMedia($media_id);
  }

  function _createTmpMedia($media_id)
  {
    Fs :: mkdir(MEDIA_DIR);
    touch(MEDIA_DIR. $media_id . '.media');
  }

  function _removeTmpMedia($media_id)
  {
    unlink(MEDIA_DIR. $media_id . '.media');
  }
}

?>