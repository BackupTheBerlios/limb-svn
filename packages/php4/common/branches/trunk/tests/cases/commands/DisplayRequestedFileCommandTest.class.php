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
require_once(dirname(__FILE__) . '/../../../commands/files/DisplayRequestedFileCommand.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/request/HttpResponse.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(LIMB_DIR . '/core/datasources/RequestedObjectDatasource.class.php');
include_once(LIMB_DIR . '/core/util/MimeType.class.php');

Mock :: generate('LimbBaseToolkit', 'MockLimbToolkit');
Mock :: generate('HttpResponse');
Mock :: generate('Request');
Mock :: generate('MimeType');
Mock :: generate('RequestedObjectDatasource');

Mock :: generatePartial('DisplayRequestedFileCommand',
                        'DisplayFileCommandTestVersion',
                        array('_getMimeType'));

class DisplayRequestedFileCommandTest extends LimbTestCase
{
  var $command;
  var $toolkit;
  var $response;
  var $request;
  var $datasource;
  var $mime;

  function setUp()
  {
    $this->command = new DisplayFileCommandTestVersion($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->response = new MockHttpResponse($this);
    $this->request = new MockRequest($this);
    $this->datasource = new MockRequestedObjectDatasource($this);
    $this->mime = new MockMimeType($this);

    $this->command->setReturnReference('_getMimeType', $this->mime);

    $this->toolkit->setReturnReference('getResponse', $this->response);
    $this->toolkit->setReturnReference('getRequest', $this->request);
    $this->toolkit->setReturnReference('getDatasource', $this->datasource, array('RequestedObjectDatasource'));

    $this->datasource->expectOnce('setRequest', array(new IsAExpectation('MockRequest')));
    $this->datasource->expectOnce('fetch');

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->response->tally();
    $this->request->tally();
    $this->datasource->tally();
    $this->mime->tally();

    Limb :: restoreToolkit();
  }

  function testPerformObjectNotFetched()
  {
    $this->datasource->setReturnValue('fetch', array());

    $this->response->expectNever('commit');

    $this->assertEqual($this->command->perform(), LIMB_STATUS_ERROR);
  }

  function testPerformNoMediaFileNotIcon()
  {
    $this->datasource->setReturnValue('fetch', $object_data = array('id' => 100,
                                                                    'mediaId' => 'yoyoyo'));

    $this->response->expectOnce('header', array('hTTP/1.1 404 Not found'));
    $this->response->expectNever('commit');

    $this->request->expectOnce('hasAttribute', array('icon'));
    $this->request->setReturnValue('hasAttribute', false, array('icon'));

    $this->assertEqual($this->command->perform(), LIMB_STATUS_ERROR);
  }

  function testPerformNoMediaFileIsIcon()
  {
    $this->datasource->setReturnValue('fetch', $object_data = array('id' => 100,
                                                                    'mediaId' => 'yoyoyo'));

    $this->response->expectOnce('header', array('hTTP/1.1 404 Not found'));
    $this->response->expectOnce('commit');

    $this->request->expectOnce('hasAttribute', array('icon'));
    $this->request->setReturnValue('hasAttribute', true, array('icon'));

    $this->command->perform();
  }

  function testPerformShowDefaultSizedIcon()
  {
    $this->datasource->setReturnValue('fetch', $object_data = array('id' => 100,
                                                                    'mediaId' => $media_id = 'yoyoyo',
                                                                    'mimeType' => $mime_type = 'file',
                                                                    'fileName' => $file_name = 'test file'));

    $this->request->expectOnce('hasAttribute', array('icon'));
    $this->request->setReturnValue('hasAttribute', true, array('icon'));
    $this->request->setReturnValue('get', '', array('icon'));

    $this->mime->expectOnce('getTypeIcon', array($mime_type));
    $this->mime->setReturnValue('getTypeIcon', $icon = 'applicationDoc', array($mime_type));

    $this->response->expectOnce('header', array('content-type: image/gif'));
    $this->response->expectOnce('readfile', array(HTTP_MIME_ICONS_DIR .
                                                  $icon . '.' .
                                                  DisplayRequestedFileCommand :: DEFAULT_ICON_SIZE .
                                                  '.gif'));
    $this->response->expectOnce('commit');

    $this->_createTmpMedia($media_id);

    $this->command->perform();

    $this->_removeTmpMedia($media_id);
  }

  function testPerformShowRequestedSizeIcon()
  {
    $this->datasource->setReturnValue('fetch', $object_data = array('id' => 100,
                                                                    'mediaId' => $media_id = 'yoyoyo',
                                                                    'mimeType' => $mime_type = 'file',
                                                                    'fileName' => $file_name = 'test file'));

    $this->request->expectOnce('hasAttribute', array('icon'));
    $this->request->setReturnValue('hasAttribute', true, array('icon'));
    $this->request->setReturnValue('get', $size = 32, array('icon'));

    $this->mime->expectOnce('getTypeIcon', array($mime_type));
    $this->mime->setReturnValue('getTypeIcon', $icon = 'applicationDoc', array($mime_type));

    $this->response->expectOnce('header', array('content-type: image/gif'));
    $this->response->expectOnce('readfile', array(HTTP_MIME_ICONS_DIR .
                                                  $icon . '.' .
                                                  $size .
                                                  '.gif'));
    $this->response->expectOnce('commit');

    $this->_createTmpMedia($media_id);

    $this->command->perform();

    $this->_removeTmpMedia($media_id);
  }

  function testPerformShowRequestedIconNoMimeFile()
  {
    $this->datasource->setReturnValue('fetch', $object_data = array('id' => 100,
                                                                    'mediaId' => $media_id = 'yoyoyo',
                                                                    'mimeType' => $mime_type = 'file',
                                                                    'fileName' => $file_name = 'test file'));

    $this->request->expectOnce('hasAttribute', array('icon'));
    $this->request->setReturnValue('hasAttribute', true, array('icon'));
    $this->request->setReturnValue('get', $size = 32, array('icon'));

    $this->mime->expectOnce('getTypeIcon', array($mime_type));
    $this->mime->setReturnValue('getTypeIcon', $icon = 'no-such-icon', array($mime_type));

    $this->response->expectOnce('header', array('content-type: image/gif'));
    $this->response->expectOnce('readfile', array(HTTP_MIME_ICONS_DIR .
                                                  'file.' .
                                                  $size .
                                                  '.gif'));
    $this->response->expectOnce('commit');

    $this->_createTmpMedia($media_id);

    $this->command->perform();

    $this->_removeTmpMedia($media_id);
  }

  function testPerformReadfile()
  {
    $this->datasource->setReturnValue('fetch', $object_data = array('id' => 100,
                                                                    'mediaId' => $media_id = 'yoyoyo',
                                                                    'mimeType' => $mime_type = 'file',
                                                                    'fileName' => $file_name = 'test file'));

    $this->request->expectOnce('hasAttribute', array('icon'));
    $this->request->setReturnValue('hasAttribute', false, array('icon'));

    $this->response->expectArgumentsAt(0, 'header', array("Content-type: {$mime_type}"));
    $this->response->expectArgumentsAt(1, 'header', array("Content-Disposition: attachment; filename=\"{$file_name}\""));
    $this->response->expectOnce('readfile', array(MEDIA_DIR . $media_id . '.media'));

    $this->_createTmpMedia($media_id);

    $this->assertEqual($this->command->perform(), LIMB_STATUS_OK);

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