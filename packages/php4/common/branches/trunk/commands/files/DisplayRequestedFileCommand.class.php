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
require_once(LIMB_DIR . '/class/core/commands/Command.interface.php');

if(!defined('HTTP_SHARED_DIR'))
  define('HTTP_SHARED_DIR', LIMB_DIR . '/shared/');

if(!defined('MEDIA_DIR'))
  define('MEDIA_DIR', VAR_DIR . '/media/');

if(!defined('HTTP_MIME_ICONS_DIR'))
  define('HTTP_MIME_ICONS_DIR', HTTP_SHARED_DIR . 'images/mime_icons/');

class DisplayRequestedFileCommand implements Command
{
  const DEFAULT_ICON_SIZE = 16;

  public function perform()
  {
    $request = Limb :: toolkit()->getRequest();
    $response = Limb :: toolkit()->getResponse();
    $datasource = Limb :: toolkit()->getDatasource('RequestedObjectDatasource');

    $datasource->setRequest($request);

    if(!$object_data = $datasource->fetch())
      return Limb :: STATUS_ERROR;

    if(!file_exists(MEDIA_DIR . $object_data['media_id'] . '.media'))
    {
      $response->header("HTTP/1.1 404 Not found");

      if(!$request->hasAttribute('icon'))
        return Limb :: STATUS_ERROR;

      $response->commit(); //for speed
      return;//for tests, fix!!!
    }

    if ($request->hasAttribute('icon'))
    {
      $this->_fillIconResponse($response, $request, $object_data);
      $response->commit();//for speed
      return;//for tests, fix!!!
    }

    $response->header("Content-type: {$object_data['mime_type']}");
    $response->header('Content-Disposition: attachment; filename="' . $object_data['file_name'] . '"');
    $response->readfile(MEDIA_DIR . $object_data['media_id'] . '.media');

    return Limb :: STATUS_OK;
  }

  protected function _fillIconResponse($response, $request, $object_data)
  {
    if (!$size = $request->get('icon'))
      $size = self :: DEFAULT_ICON_SIZE;

    $icon = $this->_getMimeType()->getTypeIcon($object_data['mime_type']);

    $file_path = HTTP_MIME_ICONS_DIR . "{$icon}.{$size}.gif";

    if (!file_exists($file_path))
      $file_path = HTTP_MIME_ICONS_DIR . "file.{$size}.gif";

    $response->header("Content-type: image/gif");
    $response->readfile($file_path);
  }

  protected function _getMimeType()
  {
    include_once(LIMB_DIR . '/class/lib/util/MimeType.class.php');
    return new MimeType();
  }
}

?>