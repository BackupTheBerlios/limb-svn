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
class MediaManager
{
  public function getMediaFilePath($media_id)
  {
    return MEDIA_DIR . $media_id . '.media';
  }

  public function unlinkMedia($media_id)
  {
    unlink($this->getMediaFilePath($media_id));
  }

  public function store($disk_file_path)
  {
    if(!file_exists($disk_file_path))
      throw new FileNotFoundException('file not found', $disk_file_path);

    srand(time());
    $media_id = md5(uniqid(rand()));

    Fs :: mkdir(MEDIA_DIR);

    $media_file = $this->getMediaFilePath($media_id);

    if (!copy($disk_file_path, $media_file))
    {
      throw new IOException('copy failed',
        array(
          'dst' => $media_file,
          'src' => $disk_file_path
          )
      );
    }
    return $media_id;
  }
}


?>
